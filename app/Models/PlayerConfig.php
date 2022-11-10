<?php

/**
 * Date: Wed, 28 Aug 2019 07:14:16 +0000.
 */

namespace App\Models;

use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Events\CachePlayerConfig;
use App\Events\RemovePlayerConfigCache;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class PlayerConfig
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $player_type
 * @property string $title
 * @property string $default_album_art
 * @property int $delay_between_audio
 * @property float $initial_playback_speed
 * @property bool $hide_playlist_in_singlemode
 * @property bool $show_playlist
 * @property bool $show_info
 * @property bool $enable_shuffle
 * @property bool $debug_player
 * @property string $player_configurable_id
 * @property string $player_configurable_type
 * @property string $feed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $preload
 *
 * @property \App\Models\User $usr
 *
 * @package App\Models
 */
class PlayerConfig extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes, GeneratesUuid, HasFactory;

    const DEFAULT_COVER_ART = '/images/podcaster_icon_weiss.svg';
    const TYPE_CHANNEL = 'channel';
    const TYPE_SHOW = 'show';
    const TYPE_DIRECT = 'direct';

    protected $primaryKey = 'id';

    protected $casts = [
		'user_id' => 'int',
        'uuid' => EfficientUuid::class,
		'player_type' => 'int',
		'delay_between_audio' => 'int',
		'initial_playback_speed' => 'float',
		'hide_playlist_in_singlemode' => 'bool',
		'show_playlist' => 'bool',
		'show_info' => 'bool',
		'enable_shuffle' => 'bool',
		'debug_player' => 'bool',
		'sharing' => 'bool',
	];

	protected $fillable = [
		'user_id',
		'uuid',
		'player_type',
		'title',
		'default_album_art',
		'delay_between_audio',
		'initial_playback_speed',
		'hide_playlist_in_singlemode',
		'show_playlist',
		'show_info',
		'enable_shuffle',
		'debug_player',
		'player_configurable_id',
		'player_configurable_type',
		'feed_id',
		'sharing',
		'text_color',
		'background_color',
		'icon_color',
		'icon_fg_color',
		'progressbar_color',
		'progressbar_buffer_color',
		'custom_styles',
		'preload',
		'order',
	];

    protected $attributes = [
        'player_type' => 1,
        'default_album_art' => self::DEFAULT_COVER_ART,
        'delay_between_audio' => 50,
        'initial_playback_speed' => 1.0,
        'hide_playlist_in_singlemode' => true,
        'show_playlist' => false,
        'show_info' => false,
        'enable_shuffle' => false,
        'debug_player' => false,
        'sharing' => false,
        'preload' => 'metadata',
        'order' => 'date_desc',
    ];

    protected static function boot()
    {
        static::saved(function(PlayerConfig $playerConfig) {
            event(new CachePlayerConfig($playerConfig));
        });

        static::updated(function(PlayerConfig $playerConfig) {
            event(new CachePlayerConfig($playerConfig));
        });

        static::deleted(function(PlayerConfig $playerConfig) {
            event(new RemovePlayerConfigCache($playerConfig));
        });

        parent::boot();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['default_album_art'] = asset(self::DEFAULT_COVER_ART);
    }

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function getSongTemplateForShow($username)
    {
        $album = trans('player.show_default_title_no_show');

        try {
            $feed = Feed::whereUsername($username)->whereFeedId($this->feed_id)->firstOrFail();
            $guid = $this->player_configurable_id;
            $shows = $feed->shows()->filter(function ($item) use ($guid) {
                return $item->guid === $guid;
            });
            $album = $feed->rss['title'];
            $this->setUpShows($shows, $username, $feed, true);
        } catch (\Exception $e) {
            $show = $this->getFallbackShow();
            $shows = new Collection([$show]);
        }

        return view('player.json.song', compact('shows', 'album'))->render();
    }

	public function getSongTemplateForShows($username)
    {
        try {
            $feed = Feed::whereUsername($username)->where('feed_id', '=', $this->feed_id)->firstOrFail();
            $shows = $feed->shows;
            $album = '';

            $sortOrder = $this->order;

            if (in_array($sortOrder, ['date_desc', 'itunes_desc'])) {
                $shows = $shows->sortByDesc(function($show) use ($sortOrder) {
                    switch ($sortOrder) {
                        case 'itunes_desc':
                            return $show->itunes['episode'];
                        case 'date_desc':
                        default:
                            return $show->lastUpdate;
                    }
                }, SORT_NUMERIC)->values();
            } else {
                $shows = $shows->sortBy(function($show) use ($sortOrder) {
                    switch ($sortOrder) {
                        case 'itunes_asc':
                            return $show->itunes['episode'];
                        case 'date_asc':
                        default:
                            return $show->lastUpdate;
                    }
                }, SORT_NUMERIC)->values();
            }

            $album = $feed->rss['title'];
            $this->setUpShows($shows, $username, $feed);
        } catch (\Exception $e) {
            $show = $this->getFallbackShow();
            $shows = new Collection([$show]);
        }

        return view('player.json.song', compact('shows', 'album'))->render();
    }

    /**
     * @param  array  $shows
     * @param  string  $username
     * @param  Feed  $feed
     * @throws \Exception
     */
    private function setUpShows(&$shows, string $username, Feed $feed, bool $acceptUnpublished = false): void
    {
        if (isset($feed->logo['itunes']) && !empty($feed->logo['itunes'])) {
            $file = get_file($username, $feed->logo['itunes']);
            if ($file && isset($file['name']) && $file['name']) {
                $this->default_album_art = get_image_uri($feed->feed_id, $file['name'], $feed->domain);
            }
        }
        $defaultCoverArt = $this->getDefaultAlbumArt();
        $aSettings = $feed->settings;

        foreach($shows as $key => &$show) {

            if ($acceptUnpublished) {
                if (!$show->show_media) {
                    // Skip shows without media
                    unset($shows[$key]);
                    continue;
                }
            } else {
                if (!in_array($show->is_public, [Show::PUBLISH_PAST, Show::PUBLISH_NOW])
                    || $show->lastUpdate > time()
                    || !$show->show_media) {
                    // Skip shows without media
                    unset($shows[$key]);
                    continue;
                }
            }

            $file = get_file($username, $show->show_media);
            if (!$file) {
                // Skip entries where no media file is found
                unset($shows[$key]);
                continue;
            }

            if (isset($aSettings['audiotakes'])
                && $aSettings['audiotakes'] == 1) {
                $collectionId = $aSettings['audiotakes_id'] ?? 'at-00000';

                if (isset($entry['audiotakes_guid']) && !empty($entry['audiotakes_guid'])) {
                    $episodeId = $collectionId . '-' . $entry['audiotakes_guid'];
                } else {
                    // This should not happen
                    $episodeId = $collectionId . '-' . sha1($file['name']);
                }

                // Categories
/*                $cats = [];
                $cats['cat'] = [];
                foreach ($feed->itunes['category'] as $_cat) {
                    $cat = urlencode($_cat['main']);

                    if (!in_array($cat, $cats['cat'])) {
                        $cats['cat'][] = $cat;
                    }

                    if (isset($_cat['sub'])) {
                        $cat .= '>' . urlencode($_cat['sub']);
                    }

                    if (!in_array($cat, $cats['cat'])) {
                        $cats['cat'][] = $cat;
                    }
                }
                if (isset($aValue['googleplay']['category'])) {
                    $_cat = $aValue['googleplay']['category'];
                    $cats['cat'][] = urlencode($_cat);
                }*/

                $show->_url = 'https://deliver.audiotakes.net/d/' . $feed->domain['subdomain'] . '.' . $feed->domain['tld'] . '/p/' . $feed->feed_id . '/m/' . "{$file['name']}?awCollectionId={$collectionId}&awEpisodeId={$episodeId}&origin=player&v=" . time()/* . '&' . http_build_query($cats)*/;
            } else {
                // TODO: Change `type` of get_direct_uri to `embed`
                $show->_url = get_direct_uri($username, 'download', $file['name'], 'webplayer');
            }
            $show->_image = $defaultCoverArt;

            if (isset($show->itunes['logo']) && $show->itunes['logo']) {
                $image = get_file($username, $show->itunes['logo']);

                if ($image) {
                    $show->_image = get_image_uri($feed->feed_id, $image['name'], $feed->domain);
                }
            }
        }
    }

    /**
     * @param  string  $username
     * @return string
     */
    public function getConfigTemplate(string $username)
    {
        $config = $this;

        if ($this->player_configurable_type == self::TYPE_SHOW) {
            $shows = $this->getSongTemplateForShow($username);
        } else {
            $shows = $this->getSongTemplateForShows($username);
        }

        $sharingLinks = [];

        if ($config->sharing) {
            $feed = Feed::whereUsername($username)->whereFeedId($this->feed_id)->first();

            if (isset($feed->rss['link'])) {
                $sharingLinks['homepage'] = $feed->rss['link'];
            } // TODO else podcaster Blog

            $sharingLinks['rss'] = get_feed_uri($this->feed_id, $feed->domain);

            if ($feed->submit_links) {
                $sharingLinks = array_merge($sharingLinks, $feed->submit_links);
            }

            if (isset($feed->settings['spotify'])
                && $feed->settings['spotify'] == 1
                && isset($feed->settings['spotify_uri'])
                && !empty($feed->settings['spotify_uri'])) {
                $aSpotifyUri = explode(':', $feed->settings['spotify_uri']);
                $spotifyUri = array_pop($aSpotifyUri);
                $sharingLinks['spotify'] = 'https://open.spotify.com/show/' . $spotifyUri;
            }

            $sharingLinks = json_encode($sharingLinks);
        }

        return view('player.json.config', compact('config', 'shows', 'sharingLinks'))->render();
    }

    public function get($username = null, $cacheTtl = 0)
    {
        if (!$username) {
            $user = User::select(['username'])->findOrFail($this->user_id);
            $username = $user->username;
        }

        return $this->getConfigTemplate($username);
    }

/*    private function getCacheKey()
    {
        return 'player_config-' . $this->user_id . '-' . $this->id;
    }*/

    public function getDefaultAlbumArt()
    {
        return $this->default_album_art;
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    private function getFallbackShow(): Show
    {
        $show = new Show();
        $show->title = trans('player.show_default_title_maintenance');
        $show->description = trans('player.show_default_description_maintenance');
        $itunes = [
            'author' => config('app.name'),
            'duration' => '00:05'
        ];
        $show->_url = 'https://www.podcaster.de/resources/player/maintenance.mp3';
        $show->_image = 'https://www.podcaster.de/images/podcaster_icon_weiss.svg';
        $show->itunes = $itunes;

        return $show;
    }
}
