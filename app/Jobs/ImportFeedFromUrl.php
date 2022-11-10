<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Classes\Domain;
use App\Classes\FeedReader;
use App\Models\Feed;
use App\Models\Show;
use App\Models\User;

class ImportFeedFromUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $feedId;
    /**
     * @var array
     */
    private $options;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  string  $url
     * @param  string  $feedId
     * @param  array  $options
     */
    public function __construct(User $user, string $url, string $feedId, array $options = [])
    {
        $this->user = $user;
        $this->url = $url;
        $this->feedId = $feedId;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $feed = \Laminas\Feed\Reader\Reader::import($this->url);

        if (!$feed instanceof \Laminas\Feed\Reader\Feed\AbstractFeed) {
            throw new \Exception("Got unexpected feed type for url `{$this->url}`.");
        }

        $aFeed = $this->getFeedArray($feed);

        if (count($aFeed['itunes']['category']) < 1) {
            $aFeed['itunes']['category'][] = Feed::ITUNES_CATEGORY_DEFAULT;
        } elseif (count($aFeed['itunes']['category']) > 3) {
            $aFeed['itunes']['category'] = array_slice($aFeed['itunes']['category'], 0, 3);
        }

        if (is_array($aFeed['itunes']['category'])) {
            $aCat   = [];
            $it     = new \ArrayIterator($aFeed['itunes']['category']);

            foreach ($it as $key => $val) {
                if (!is_null($val) && is_array($val)) {
                    $sit = new \ArrayIterator($val);
                    $aCat[] = $key . ':' . $sit->key();
                } else {
                    $aCat[] = $key;
                }
            }
            $aFeed['itunes']['category'] = $aCat;
        }

        if (empty($aFeed['rss']['author'])) {
            if (!empty($aFeed['itunes']['author'])) {
                $aFeed['rss']['author'] = $aFeed['itunes']['author'];
            } else {
                $aFeed['rss']['author'] = $this->user->username;
            }
        }

        if (empty($aFeed['rss']['copyright'])) {
            if (!empty($aFeed['itunes']['author'])) {
                $aFeed['rss']['copyright'] = $aFeed['itunes']['author'];
            } else {
                $aFeed['rss']['copyright'] = $this->user->username;
            }
        }

/*        foreach ($aFeed['entries'] as &$aValue) {
            try {
                $mediaId                 = $mediaDAO->add($aValue['enclosure'][0]['url'], $username);
                $aValue['guid']          = $aValue['show_media'] = $mediaId;
                unset($aValue['id']);
                unset($aValue['enclosure']);
            } catch (Exception $e) {
                $aValue['guid']          = uniqid('pod-');
                unset($aValue['id']);
                unset($aValue['enclosure']);
                SGL::logMessage($e->getMessage(), PEAR_LOG_CRIT);
            }

            if (empty($aValue['author'])) {
                if (!empty($aFeed['itunes']['author'])) {
                    $aValue['author'] = $aFeed['itunes']['author'];
                } elseif (!empty($aFeed['rss']['author'])) {
                    $aValue['author'] = $aFeed['rss']['author'];
                } elseif (!empty($aFeed['rss']['title'])) {
                    $aValue['author'] = $aFeed['rss']['title'];
                } else {
                    $aValue['author'] = $username;
                }
            }

            if (isset($aValue['image']) && $aValue['image']) {
                $aValue[self::LOGO_TYPE_ITUNES]['logo'] = $mediaDAO->add($aValue['image'], $username);
            }
        }

        if ($aFeed['rss']['image']) {
            try {
                $aFeed['logo']['rss'] = $mediaDAO->add($aFeed['rss']['image'], $username);
            } catch (\Exception $e) {
            }
        }

        if ($aFeed['itunes']['image']) {
            try {
                $aFeed['logo']['itunes'] = $mediaDAO->add($aFeed['itunes']['image'], $username);
            } catch (\Exception $e) {
            }
        }*/

        $aFeed = array_merge($aFeed, $this->options);
        $feedId = $this->getValidFeedId($this->feedId);
        $feedId = trim($feedId);

        if (!$feedId) {
            throw new \Exception("An empty feed ID is not allowed.");
        }

        // TODO: Check that feedId is unique for user
        if (Feed::whereUsername($this->user->username)->whereFeedId($feedId)->count() > 0) {
            throw new \Exception("Feed ID `{$feedId}` is already taken.");
        }

        if ($this->isForbidden($feedId)) {
            throw new \Exception("Feed ID `{$feedId}` is not available.");
        }

        $feed = new Feed($aFeed);
        $feed->username = $this->user->username;
        $feed->feed_id = $feedId;
        $feed->domain = (new Domain)->getDomainDefaults($this->user->username);
        $feed->save();

    }

    /**
     * Filter out any chars that may not be in a filename
     * '/' bzw. DIRECTORY_SEPARATOR, . , , ,
     *
     * @param string $feedId
     * @return string
     */
    private function getValidFeedId($feedId)
    {
        $feedId        = str_replace(" ", "-", $feedId);
        $disallowed    = preg_quote('/:*?"<>|.;`,$!()%=&#+\'\\','/');
        $res = preg_replace_callback("/([{$disallowed}])/", function($matches) {
            return '-'; }, $feedId);

        return $res;
    }

    /**
     * @param  string  $feedId
     * @return bool
     */
    public function isForbidden(string $feedId): bool
    {
        if (Str::contains($feedId, 'ß')) {
            return true;
        }
        return in_array($feedId, ['anlegen','wizard','erstellen','feed']); // TODO: I18N
    }

    /**
     * @param  \Laminas\Feed\Reader\Feed\AbstractFeed  $oFeed
     * @return array[]
     */
    private function getFeedArray(\Laminas\Feed\Reader\Feed\AbstractFeed $oFeed): array
    {
        $feedReader = new FeedReader();
        $oPodcast       = $oFeed->getExtension('Podcast');
        $oGooglePlay    = $oFeed->getExtension('GooglePlayPodcast');
        $xpathPrefix    = $oFeed->getXpathPrefix();
        $xpath          = $oFeed->getXpath();
        $published      = $oFeed->getDateCreated();

        if (!is_null($published)) {
            $published = $published->format(DATE_W3C);
        } else {
            $published  = Carbon::now()->toW3cString();
        }
        $image          = null;
        $image          = $xpath->evaluate('string(' . $xpathPrefix . '/image/url)');

        if (!$image) {
            $image      = $xpath->evaluate('string(' . $xpathPrefix . '/image)');
        }

        if (!$image) {
            $image      = $oPodcast->getItunesImage();
        }

        if (!$image) {
            $image      = $oGooglePlay->getPlayPodcastImage();
        }
        $aItunesCat     = $oPodcast->getItunesCategories();
        $email          = $xpath->evaluate('string(' . $xpathPrefix . '/managingEditor)');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $feedReader->getEmail($oFeed);
        }

        $googlePlayCategories = $oGooglePlay->getPlayPodcastCategories();
        $googlePlayCategory = null;

        if ($googlePlayCategories) {
            $googlePlayCategory = array_keys($googlePlayCategories)[0];
        }

        $aFeed = [
            'rss' => [
                'title'                  => $oFeed->getTitle(),
                'description'            => $oFeed->getDescription() ?? $oFeed->getTitle(),
                'link'                   => $oFeed->getLink(),
                'author'                 => $feedReader->getName($oFeed),
                'published'              => $published,
                'language'   	         => $oFeed->getLanguage(),
                'copyright'  	         => $oFeed->getCopyright(),
                'email'      	         => $email,
                'managingEditor'         => $email,
                'rating'                 => $xpath->evaluate('string(' . $xpathPrefix . '/rating)'),
                'category'               => implode(',', $oFeed->getCategories()->getValues()),
                'image'                  => $image,
            ],
            'entries' => [],
            'itunes' => [
                'author'    => $oPodcast->getCastAuthor(),
                'email'		=> $oPodcast->getOwner(),
                'subtitle'	=> $oPodcast->getSubtitle(),
                'summary'	=> $oPodcast->getSummary(),
                //'keywords'	=> $oPodcast->getKeywords(),
                'block'		=> $oPodcast->getBlock(),
                'explicit'	=> filter_var($oPodcast->getExplicit(), FILTER_VALIDATE_BOOLEAN),
                'image'     => $oPodcast->getItunesImage(),
                'category'  => $aItunesCat,
            ],
            'googleplay' => [
                'author' => $oGooglePlay->getPlayPodcastAuthor(),
                'block' => $oGooglePlay->getPlayPodcastBlock(),
                'category' => $googlePlayCategory,
                'description' => $oGooglePlay->getPlayPodcastDescription(),
                'explicit' => filter_var($oGooglePlay->getPlayPodcastExplicit(), FILTER_VALIDATE_BOOLEAN),
                'image' => $oGooglePlay->getPlayPodcastImage(),
            ]
        ];

        // Loop over each channel item and store relevant data
        foreach ($oFeed as $key => $item) {
            $enclosure           = $item->getEnclosure();
            $aEnclosure = [];
            $itunes = $item->getExtension('Podcast');

            if ($enclosure) {
                $aEnclosure = [
                    'url' => $enclosure->url,
                    'length' => $enclosure->length,
                    'type' => $enclosure->type,
                ];
            }
            $author              = $item->getAuthor();
            $published           = $item->getDateCreated();

            if (!is_null($published)) {
                $published = $published->format(DATE_W3C);
            } else {
                $published  = Carbon::now()->toW3cString();
            }

            $item->getXpath();
            $image = $xpath->evaluate('string(//itunes:image/@href)');
            $aData = [
                'id'            => $item->getId(),
                'title'       	=> $item->getTitle(),
                'link'        	=> $item->getLink(),
                'description' 	=> $item->getDescription() ?? $item->getTitle() ?? 'Podcast',
                'author'        => $author["name"] ?? $oPodcast->getCastAuthor() ?? 'Podcaster',
                'lastUpdate'    => $published,
                'is_public'     => Show::PUBLISH_NOW,
                'itunes' => [
                    'title' => $itunes->getTitle(),
                    'subtitle' => $itunes->getSubtitle(),
                    'summary' => $itunes->getSummary(),
                    'author' => $itunes->getCastAuthor(),
                    'image' => $itunes->getItunesImage(),
                    'block' => $itunes->getBlock(),
                    'explicit' => filter_var($itunes->getExplicit(), FILTER_VALIDATE_BOOLEAN),
                    'duration' => $itunes->getDuration(),
                    'season' => $itunes->getSeason(),
                    'episode' => $itunes->getEpisode(),
                    'episodeType' => $itunes->getEpisodeType(),
                    'isclosedcaptioned' => $itunes->isClosedCaptioned(),
                ],
                'enclosure' => $aEnclosure,
            ];

            if ($image) {
                $aData['image'] = $image;
            }

            $aFeed['entries'][] = $aData;
        }

        return $aFeed;
    }

}
