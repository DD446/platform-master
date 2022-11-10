<?php

namespace App\Models;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Events\FeedRemovalEvent;
use App\Events\FeedUpdateEvent;

class Feed extends Model
{
    use HasFactory;

    const DEFAULT_EXTENSION = '.rss';
    const FEED_COUNT_DEFAULT = 100;
    const FEED_COUNT_UNLIMITED = -1;
    const ITUNES_CATEGORY_DEFAULT  = 'Leisure';

    const WEBSITE_TYPE_NONE        = false;
    const WEBSITE_TYPE_WORDPRESS   = 'wordpress';
    const WEBSITE_TYPE_REDIRECT   = 'website_redirect';
    // new hosting / new statistics
    const BASE_URL_CDN = 'https://cdn.podcast-hosting.org';
    // audiotakes enabled feeds
    const BASE_URL_ADN = 'https://deliver.audiotakes.net';
    const BASE_URL_LDN = 'https://lc.podcast-hosting.org';
    const BASE_URL_MAIN_CDN = 'https://main.podcast-hosting.org';

    protected $connection = 'mongodb';
    protected $primaryKey = 'feed_id';

    protected $fillable = [
        'rss',
        'itunes',
        'googleplay',
        'podcast',
        'domain',
        'settings',
        'logo',
        'entries',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '_id',
        'settings',
        'submit_links',
    ];

    protected static function boot()
    {
        static::saved(function(Feed $feed) {
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));
            LaravelMatomoTracker::queueEvent('feed', 'saved', $feed->username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
        });

        static::updated(function(Feed $feed) {
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));
            LaravelMatomoTracker::queueEvent('feed', 'updated', $feed->username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
        });

        static::deleted(function (Feed $feed) {
            event(new FeedRemovalEvent($feed));
            LaravelMatomoTracker::queueEvent('feed', 'deleted', $feed->username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
        });

        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'username', 'username');
    }

    public function shows()
    {
        return $this->embedsMany('App\Models\Show', 'entries');
    }

    public function scopeOwner($query)
    {
        return $query->where('username', '=', new \MongoDB\BSON\Regex('^' . preg_quote(auth()->user()->username) . '$', 'i'));
    }

    public function setFeedIdAttribute($value)
    {
        $value = str_replace('ß', 'ss', Str::lower($value));

        $this->attributes['feed_id'] = $value;
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @param $mediaId
     * @return mixed
     */
    public static function setLogo(string $username, string $feedId, $mediaId)
    {
        $feed = Feed::whereUsername($username)->whereFeedId($feedId)->firstOrFail();
        $logo = $feed->logo ?? [];
        $_logo = array_merge($logo, ['rss' => $mediaId, 'itunes' => $mediaId]);

        return $feed->whereUsername($feed->username)
            ->whereFeedId($feedId)
            ->update(
                [
                    'logo' => $_logo,
                ]
            );
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @return mixed
     */
    public static function deleteLogo(string $username, string $feedId)
    {
        $feed = Feed::whereUsername($username)->whereFeedId($feedId)->firstOrFail();

        return $feed->whereUsername($feed->username)
            ->whereFeedId($feedId)
            ->update(
                [
                    'logo' => null,
                ]
            );
    }

    public function getLogo()
    {
        $logo = '';

        try {
            if (isset($this->logo['itunes']) && is_numeric($this->logo['itunes'])) {
                $file = get_file($this->username, $this->logo['itunes']);

                if ($file) {
                    $logo = get_image_uri($this->feed_id, $file['name'], $this->domain);
                }
            }
        } catch (\Exception $e) {
        }

        return $logo;
    }

    public function getSubdomainAttribute()
    {
        if ($this->domain
            && isset($this->domain['subdomain'])
            && $this->domain['subdomain']
            && isset($this->domain['tld'])
            && $this->domain['tld']) {
            return $this->domain['subdomain'] . '.' . $this->domain['tld'];
        }

        $domain = get_default_domain($this->username);

        return $domain['subdomain'] . '.' . $domain['tld'];
    }

    public function usesWebsite()
    {
        return isset($this->domain)
            && isset($this->domain['website_type'])
            && $this->domain['website_type'] != self::WEBSITE_TYPE_NONE
            && $this->domain['website_type'] != self::WEBSITE_TYPE_REDIRECT;
    }

    public function audiotakes_contract()
    {
        return $this->hasOne(AudiotakesContract::where('user_id', '=', $this->user->id), 'feed_id', 'feed_id');
    }
}
