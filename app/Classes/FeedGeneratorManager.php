<?php
/**
 * User: fabio
 * Date: 26.09.20
 * Time: 08:21
 */

namespace App\Classes;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laminas\Feed\Writer\Exception\RuntimeException;
use App\Models\Feed;
use App\Models\Show;
use App\Models\User;
use App\Models\UserData;

class FeedGeneratorManager
{
    const GENERATOR_VERSION = '4.1';
    const GENERATOR_SIG = 'Feedarator';
    const GENERATOR_EMAIL_TECH = 'feeds@podcaster.de';
    const GENERATOR_NAME_TECH = 'Fabio Bacigalupo';
    const DEFAULT_CHARSET = 'utf-8';
    const TIME_TO_LIVE = 82400;
    const ITUNES_CATEGORY_DEFAULT  = 'Leisure';
    const PUBSUBHUBBUB_HUB = 'https://podcaster.superfeedr.com/';
    const PUBSUBHUBBUB_HUB2 = 'https://pubsubhubbub.appspot.com/';
    const FEED_COUNT_DEFAULT = Feed::FEED_COUNT_DEFAULT;
    const FEED_COUNT_UNLIMITED = Feed::FEED_COUNT_UNLIMITED;

    private string $username;
    private string $feedId;

    /**
     *
     * @param string $feedId
     * @param string $username
     */
    public function __construct(string $username, string $feedId)
    {
        $this->username = $username;
        $this->feedId = $feedId;
    }

    /**
     * @param  array  $export
     * @return object
     * @throws \Exception
     */
    private function setData(array $export): object
    {
        $username = $this->username;
        $user = User::whereUsername($username)->first();
        $feedId = $this->feedId;
        $aValue = $export['data'];
        $aSettings = $export['settings'];

        $aValue['charset'] = self::DEFAULT_CHARSET;

        if (isset($aValue['itunes']['category']) && count($aValue['itunes']['category']) > 0) {
            // Throw away empty entries in array
            $aValue['itunes']['category'] = array_filter($aValue['itunes']['category']);
        }

        if (!isset($aValue['itunes']['category']) || count($aValue['itunes']['category']) < 1) {
            $aValue['itunes']['category'][] = self::ITUNES_CATEGORY_DEFAULT;
        }

        try {
            $aDomain = $this->getDomain($username, $feedId);
            $hostname = strtolower($aDomain['hostname']);

/*            $extensionManager = new \Laminas\Feed\Writer\StandaloneExtensionManager();
            $extensionManager->remove('DublinCore\Renderer\Entry');
            $extensionManager->remove('DublinCore\Renderer\Feed');
            \Laminas\Feed\Writer\Writer::setExtensionManager($extensionManager);*/

            $feed = new FeedWriter();

            if (Str::contains($aValue['title'], ["'", '"'])) {
                $aValue['title'] = htmlspecialchars($aValue['title']);
            }

            $feed->setTitle(Str::limit($aValue['title'], 255) ?: 'Podcast'); // TOOD: I18N
            $description = $this->addLineBreaks($aValue['description'] ?? $aValue['title'] ?? 'Podcast');

            if (!$description) {
                $description = 'Podcast';
            }

            $feed->setDescription($description);
            $feed->setLink($aValue['link'] ?? config('app.url'));
            $feed->setFeedLink($hostname . "/{$feedId}.rss", 'rss');
            $feed->setFeedLink($hostname . "/{$feedId}.rss", 'atom');
            $feed->setDateModified(time());

            if (!isset($aSettings['protection']) || $aSettings['protection'] != 1) {
                $feed->addHub(self::PUBSUBHUBBUB_HUB);
                $feed->addHub(self::PUBSUBHUBBUB_HUB2);
            }

            //$feed->setWebmaster(self::getWebmaster(), GeneratorFeed::GENERATOR_NAME_TECH); # TODO
            $feed->setGenerator(self::getSignature(), self::getVersion(), self::getUrl());

            if (isset($aValue['image']) && $aValue['image']) {
                Log::debug("User: '$username': Feed: '{$feedId}': Using RSS image `{$aValue['image']}`.");
                try {
                    $feed->setImage([
                        'uri' => $aValue['image'],
                        'title' => $aValue['title'] ?: 'Podcast',
                        'link' => $aValue['link'] ?? config('app.url'),
                    ]);
                } catch (\Exception $e) {
                    Log::error("User: '$username': Feed: '{$feedId}': Setting RSS image `{$aValue['image']}` failed.");
                }
            } // Fallback is down below in itunes section

            $feed->setLanguage($aValue['language'] ?? config('app.locale'));
            $feed->addAuthor(['name' => $aValue['author'] ?? 'Podcaster', 'email' => $aValue['email'] ?? null]);
            $feed->setWebmaster("webmaster-feeds@podcaster.de", "Fabio Bacigalupo");
            $feed->setCopyright($aValue['copyright'] ?? $aValue['author'] ?? 'Podcaster');

            if (isset($aValue['category']) && !empty($aValue['category'])) {
                $categories = explode(",", $aValue['category']);
                array_walk($categories, function (&$v) {
                    $v = ['term' => trim($v)];
                });
                $feed->addCategories($categories);
            } else {
                $feed->addCategory(['term' => 'Podcast']);
            }

            // TODO: Add rel=payment (channel) - needs form field to enter data

            foreach ($aValue['itunes'] as $key => $value) {
                switch ($key) {
                    case 'explicit' :
                        $feed->setItunesExplicit(filter_var($value, FILTER_VALIDATE_BOOLEAN));
                        break;
                    case 'block' :
                        $feed->setItunesBlock($value);
                        break;
                    case 'category' :
                        $cats = [];
                        foreach ($value as $aCat) {
                            // The html_entity_decode handles a problem with &amp; being passed in which double
                            // decodes to &amp;amp; which makes the iTunes submission fail
                            if (is_array($aCat) && array_key_exists('sub', $aCat)) {
/*                                $cat = [html_entity_decode($aCat['main']) => [html_entity_decode($aCat['sub'])]];

                                if (!array_key_exists(html_entity_decode($aCat['main']), $cats)) {
                                    $cats[html_entity_decode($aCat['main'])] = [html_entity_decode($aCat['sub'])];
                                } else {
                                    array_push($cats[html_entity_decode($aCat['main'])], html_entity_decode($aCat['sub']));
                                }*/
                                $cats[html_entity_decode($aCat['main'])] = [html_entity_decode($aCat['sub'])];
                            } else {
                                //$cat = (array)['main' => html_entity_decode($aCat['main'])];
                                if (is_array($aCat)) {
                                    array_push($cats, html_entity_decode($aCat['main']));
                                } else {
                                    array_push($cats, html_entity_decode($aCat));
                                }
                            }
                        }
                        $feed->setItunesCategories($cats);
                        break;
                    case 'subtitle' :
                        if (isset($value) && $value && !empty($value)) {
                            $feed->setItunesSubtitle($value);
                        }
                        break;
                    /*                    case 'keywords' :
                                            $feed->setItunesKeywords(explode(',', $value));
                                            break;*/
                    case 'new-feed-url' :
                        // TODO: Also do not allow a link to itself
                        if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                            $feed->setItunesNewFeedUrl($value);
                        }
                        break;
                    case 'image' :
                        try {
                            if (isset($value) && $value) {
                                $feed->setItunesImage($value);
                            }
                        } catch (\Exception $e) {
                            Log::critical("ERROR: User: '$username': Could not set itunes image '{$feedId}'.");
                            Log::critical($e->getTraceAsString());
                        }
                        break;
                    case 'complete' :
                        $feed->setItunesComplete($value == 'yes');
                        break;
                    case 'type' :
                        $feed->setItunesType($value);
                        break;
                    default :
                        break;
                }
            }
            $feed->addItunesOwner(['name' => $aValue['author'] ?? 'Podcaster', 'email' => $aValue['email'] ?? 'podcast@example.org']);
            $feed->addItunesAuthor($aValue['author'] ?? 'Podcaster');
            $feed->setItunesSummary($this->getValidItunesSummary($aValue['description'] ?? $aValue['title'] ?? null));
            $feed->setLastBuildDate();

            if (isset($aValue['googleplay'])) {
                foreach ($aValue['googleplay'] as $key => $value) {
                    if (empty($value)) continue;

                    switch ($key) {
                        case 'author':
                            $feed->setPlayPodcastAuthor($value);
                            break;
                        case 'block':
                            $feed->setPlayPodcastBlock($value);
                            break;
                        case 'category':
                            $feed->setPlayPodcastCategories((array)html_entity_decode($value));
                            break;
                        case 'description':
                            $feed->setPlayPodcastDescription($this->addLineBreaks($value));
                            break;
                        case 'explicit':
                            $feed->setPlayPodcastExplicit($value);
                            break;
                        case 'image':
                            $feed->setPlayPodcastImage($value);
                            break;
                    }
                }
            }
            $iEntry = 1;
            $iEntryMax = $aSettings['feed_entries'] ?? self::FEED_COUNT_DEFAULT;

            if (isset($aValue['entries'])) {
                foreach ($aValue['entries'] as $entry) {

                    if (isset($entry['is_public']) && ((int)$entry['is_public'] == Show::PUBLISH_FUTURE
                            || (int)$entry['is_public'] == Show::PUBLISH_DRAFT)) {
                        continue;
                    }

                    if (isset($entry['link']) && strpos($entry['link'], '://') === false) {
                        if (!empty($entry['link'])) {
                            $entry['link'] = $hostname."/".$entry['link'];
                        } else {
                            // Prevent `Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI` exception
                            $entry['link'] = $hostname;
                        }
                    }

                    $item = $feed->createEntry();

                    if (Str::contains($entry['title'], ["'", '"'])) {
                        $entry['title'] = htmlspecialchars($entry['title']);
                    }
                    $item->setTitle($entry['title'] ?: 'Episode'); // TOOD: I18N
                    $item->setLink($entry['link'] ?? config('app.url'));

                    // No website to point link to
                    if (!isset($aDomain['website_type']) || !$aDomain['website_type'] && strpos($entry['link'], $hostname) === 0) {
                        if (isset($entry['enclosure'][0]) && !empty($entry['enclosure'][0]['url'])) {
                            $item->setLink($entry['enclosure'][0]['url']);
                        }
                    }
                    $authorData = [
                        'name' => $entry['author'] ?? 'Podcaster',
                        'email' => $aValue['email'] ?? 'feeds-webmaster@podcaster.de', # TODO Replace
                        'uri' => $entry['link'] ?? config('app.url'),
                    ];

                    try {
                        $item->addAuthor($authorData);
                    } catch (\Exception $e) {
                        $msg = trans("feeds.error_generator_check_url_for_entry", ['title' => $entry['title']]);
                        Log::critical("ERROR: User '{$username}': Failed generating feed '{$feedId}': ".$msg);
                        throw new \Exception($msg);
                    }

                    try {
                        $d = (new \DateTime())->setTimestamp($entry['lastUpdate'] ?? time());
                        $item->setDateModified($d);
                        $item->setDateCreated($d);
                    } catch (\Exception $e) {
                        $item->setDateModified(time());
                        $item->setDateCreated(time());
                        Log::critical($e->getTraceAsString());
                    }

                    if (isset($entry['guid']) && !empty($entry['guid'])) {
                        $item->setId($entry['guid']);
                    } else {
                        // This should not happen
                        // TODO: Create job to set missing GUID
                    }
                    $description = $this->addLineBreaks($entry['description'] ?? $entry['title']);

                    if (isset($aSettings['downloadlink'])
                        && $aSettings['downloadlink'] == 1
                        && !empty($entry['enclosure'][0]['url'])) {
                        $description .= PHP_EOL.PHP_EOL
                            . self::getDirectDownloadLink($hostname, basename($entry['enclosure'][0]['url']),
                                $aSettings['downloadlink_description'] ?? null);
                    }

                    if (!$description) {
                        $description = 'Podcast';
                    }

                    $item->setDescription($description);
                    // xmlns:content - Makes HTML view on mobile iTunes possible
                    $item->setContent($description);

                    if (isset($entry['enclosure'][0]) && !empty($entry['enclosure'][0]['url'])) {
                        $entry['enclosure'] = $entry['enclosure'][0];
                        $entry['enclosure']['uri'] = $entry['enclosure']['url'];

                        if (isset($aSettings['audiotakes'])
                            && $aSettings['audiotakes'] == 1) {
                            $aUrl = parse_url($entry['enclosure']['uri']);
                            $collectionId = $aSettings['audiotakes_id'] ?? 'at-00000';
                            $filename = pathinfo($aUrl['path'], PATHINFO_FILENAME);
                            $ext = pathinfo($aUrl['path'], PATHINFO_EXTENSION);
                            $file = $filename . '.' . $ext;

                            if (isset($entry['audiotakes_guid']) && !empty($entry['audiotakes_guid'])) {
                                $episodeId = $collectionId . '-' . $entry['audiotakes_guid'];
                            } else {
                                // This should not happen
                                // TODO: Make job to save episodeId
                                $episodeId = $collectionId . '-' . sha1($file);
                            }

                            // Categories
                            $cats = [];
                            $cats['cat'] = [];
                            foreach ($aValue['itunes']['category'] as $_cat) {
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
                            }

                            if (in_array($username, ['w8gez1', 'ofietj'])) {
                                $entry['enclosure']['uri'] = Feed::BASE_URL_MAIN_CDN . '/' . Str::after($entry['enclosure']['uri'], 'https://') . "?awCollectionId={$collectionId}&awEpisodeId={$episodeId}&origin=feed&v=" . time();
                                //$entry["enclosure']['uri'] = Feed::BASE_URL_LDN . '/d/' . $aUrl['host'] . '/p/' . $feedId . '/m/' . "{$file}?awCollectionId={$collectionId}&awEpisodeId={$episodeId}&origin=feed&v=" . time();
                            } else {
                                $entry['enclosure']['uri'] = Feed::BASE_URL_ADN . '/d/' . $aUrl['host'] . '/p/' . $feedId . '/m/' . "{$file}?awCollectionId={$collectionId}&awEpisodeId={$episodeId}&origin=feed&v=" . time()/* . '&' . http_build_query($cats)*/;
                            }
                        } elseif (isset($aSettings['chartable'])
                            && $aSettings['chartable'] == 1
                            && !empty($aSettings['chartable_id'])) {
                            $entry['enclosure']['uri'] = 'https://chtbl.com/track/'.$aSettings['chartable_id'].'/'.$entry['enclosure']['uri'];
                        } elseif (isset($aSettings['podcorn'])
                            && $aSettings['podcorn'] == 1) {
                            $entry['enclosure']['uri'] = 'https://pdcn.co/e/'.$entry['enclosure']['uri'];
                        } elseif (isset($aSettings['podtrac'])
                            && $aSettings['podtrac'] == 1) {
                            $aUrl = parse_url($entry['enclosure']['uri']);
                            $ext = pathinfo($aUrl['path'], PATHINFO_EXTENSION);
                            $entry['enclosure']['uri'] = 'https://dts.podtrac.com/redirect.'.$ext.'/'.$aUrl['host'].$aUrl['path'];
                        } elseif (isset($aSettings['rms'])
                            && $aSettings['rms'] == 1
                            && !empty($aSettings['rms'])) {
                            // Rewrite url for RMS ads
                            // https://rmsi-podcast.de/comedyperiode/media/42_Folge.mp3?awCollectionId=STO0001
                            $path = parse_url($entry['enclosure']['uri'], PHP_URL_PATH);
                            $entry['enclosure']['uri'] = 'https://rmsi-podcast.de'.$path.'?awCollectionId='.($aSettings['rms_id'] ?? 'STO0001');
                        } elseif ($user->use_new_statistics) {
                            $aUrl = parse_url($entry['enclosure']['uri']);
                            $filename = pathinfo($aUrl['path'], PATHINFO_FILENAME);
                            $ext = pathinfo($aUrl['path'], PATHINFO_EXTENSION);
                            $file = $filename . '.' . $ext;
                            $episodeId = $entry['guid'] ?: '';
                            $entry['enclosure']['uri'] = Feed::BASE_URL_CDN . '/d/' . $aUrl['host'] . '/p/' . $feedId . '/m/' . "{$file}?awEpisodeId={$episodeId}&origin=feed&v=" . time();
                        }
                        $item->setEnclosure($entry['enclosure']);
                    }

                    if (isset($entry['itunes']['title']) && $entry['itunes']['title']) {
                        if (Str::contains($entry['itunes']['title'], ["'", '"'])) {
                            $entry['itunes']['title'] = htmlspecialchars($entry['itunes']['title']);
                        }
                        $item->setItunesTitle(Str::limit($entry['itunes']['title'], 250));
                    }

                    if (isset($entry['itunes']['subtitle']) && $entry['itunes']['subtitle']) {
                        $item->setItunesSubtitle($entry['itunes']['subtitle']);
                    }

                    if (isset($entry['itunes']['summary'])
                        && $entry['itunes']['summary']
                        && !empty($entry['itunes']['summary'])
                        && strlen($entry['itunes']['summary']) > 0) {
                        $item->setItunesSummary($this->getValidItunesSummary($entry['itunes']['summary']));
                    } else {
                        $item->setItunesSummary($this->getValidItunesSummary($description));
                    }

                    if (isset($entry['itunes']['author']) && $entry['itunes']['author']) {
                        $item->addItunesAuthor($entry['itunes']['author']);
                    }
                    /*                if (isset($entry['itunes']['keywords']) && $entry['itunes']['keywords']) {
                                        $item->setItunesKeywords(array_unique(array_slice(explode(",", trim($entry['itunes']['keywords'], " ,;")), 0, 11)));
                                    }*/
                    if (isset($entry['itunes']['duration']) && $entry['itunes']['duration']) {
                        $item->setItunesDuration($entry['itunes']['duration']);
                    }

                    if (isset($entry['itunes']['logo']) && $entry['itunes']['logo']) {
                        // Should be an url by now
                        if (is_numeric($entry['itunes']['logo'])) {
                            Log::critical("ERROR: User '{$username}': iTunes Logo export for feed '{$feedId}' and media '{$entry['itunes']['logo']}' is still numeric.");
                        } else {
                            try {
                                $item->setItunesImage($entry['itunes']['logo']);
                            } catch (\Exception $e) {
                                Log::critical("ERROR: User '{$username}': Failed setting image for feed '{$feedId}': ".PHP_EOL." Folge: {$entry['title']}".PHP_EOL." Bild-Eintrag: {$entry['itunes']['logo']}");
                                Log::critical($e->getTraceAsString());
                                //throw new Exception("Der Feed konnte nicht aktualisiert werden. Das Logo zur Folge '". $entry['title'] ."' ist eingebunden, aber nicht mehr vorhanden.");
                            }
                        }
                    }

                    if (isset($entry['itunes']['explicit']) && $entry['itunes']['explicit']) {
                        $item->setItunesExplicit(filter_var($entry['itunes']['explicit'], FILTER_VALIDATE_BOOLEAN));
                    }

                    if (isset($entry['itunes'])) {
                        foreach ($entry['itunes'] as $key => $value) {
                            if (empty($value)) {
                                continue;
                            }

                            switch ($key) {
                                case 'episode':
                                    $item->setItunesEpisode($value);
                                    break;
                                case 'season':
                                    $item->setItunesSeason($value);
                                    break;
                                case 'episodeType':
                                    $item->setItunesEpisodeType($value);
                                    break;
                                case 'isclosedcaptioned':
                                    $item->setItunesIsClosedCaptioned($value == 'yes');
                                    break;
                            }
                        }
                    }

                    // TODO: Add rel=payment (show) - needs form field to enter data
                    $feed->addEntry($item);
                    ++$iEntry;

                    if ($iEntryMax != self::FEED_COUNT_UNLIMITED && $iEntry > $iEntryMax) {
                        break;
                    }
                }
            }
            return $feed;
        } catch (RuntimeException $e) {
            Log::critical("ERROR: User '{$username}': Failed generating feed '{$feedId}': ");
            Log::critical($e->getTraceAsString());
            throw new \Exception("ERROR: " . $e->getMessage());
        }
    }

    /**
     *
     *
     * @return string
     */
    private function save()
    {
        try {
            $export = $this->getExport();
            $feed = $this->setData($export);
        } catch (\Exception $e) {
            Log::critical("ERROR: User: '{$this->username}': Could not generate feed '{$this->feedId}'.");
            Log::critical($e->getMessage());
            Log::critical($e->getTraceAsString());
            throw new \Exception($e);
        }

        if (isset($export['settings']['styled_feed']) && $export['settings']['styled_feed']) { // Render feed (creates DOMDocument object)
            $style = 'podcast';

            if (isset($export['settings']['styled_feed_theme']) && $export['settings']['styled_feed_theme']) {
                $style = $export['settings']['styled_feed_theme'];
            }

            // Custom
            if ($this->username == 'apwnrb' && $this->feedId == 'talk-dominos-to-me') {
                $style = 'black';
            }

            $writer = new \Laminas\Feed\Writer\Renderer\Feed\Rss($feed);
            $writer->ignoreExceptions();
            $writer->setType('rss');
            $writer->render();// Add processing instruction
            $writer->getElement()->parentNode->insertBefore(
                $writer->getDomDocument()->createProcessingInstruction(
                    'xml-stylesheet',
                    'title="'.config('app.name').' XSLT Stylesheet" type="text/xsl" href="/assets/xslt/' . $style . '.xsl"'
                ),
                $writer->getElement()
            );
            // Ouput XML
            return $writer->saveXml();
        }

        return $feed->export('rss', $ignoreExceptions = true);
    }

    /**
     *
     *
     * @param string $str
     * @return string
     */
    private function getValidItunesSummary($str) {

        $txt = html_entity_decode(htmlspecialchars_decode(strip_tags($this->addLineBreaks($str))));

        if (mb_strlen($txt) <= 4000) {
            return $txt;
        }

        $txt = mb_strcut($txt, 0, 3995);

        return $txt . '...';
    }

    private function addLineBreaks($description)
    {
        return preg_replace('#<br\s*\/?>|<\/p>|<\/div>#', '$0' . PHP_EOL, $description);
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @return array
     */
    public function getDomain(string $username, string $feedId): array
    {
        $feed = Feed::where('username', '=', new \MongoDB\BSON\Regex('^' . preg_quote($username) . '$', 'i'))
            ->whereFeedId($feedId)
            ->select(['domain'])
            ->first();

        $aDomain = $feed->domain ?? get_default_domain($username);

        // Migrate to https
        if (!isset($aDomain['protocol']) || $aDomain['protocol'] == 'http') {
            $aDomain['protocol'] = 'https';
            $hostname = $aDomain['protocol'] . '://' . $aDomain['subdomain'] . '.' . $aDomain['tld'];
            $aDomain['hostname'] = $hostname;
        }

        return $aDomain;
    }

    public static function getVersion(): string
    {
        return self::GENERATOR_VERSION;
    }

    public static function getIdentifier() {

        return self::getSignature() . '/' . self::getVersion() . ' (' . self::getUrl() . ')';
    }

    public static function getSignature() {

        return config('app.domain') . " " . self::GENERATOR_SIG;
    }

    public static function getUrl() {

        return config('app.url');
    }

    public static function getWebmaster() {

        #return self::GENERATOR_EMAIL_TECH . '(' . self::GENERATOR_NAME_TECH . ')'; // Zend does not accept Name
        return self::GENERATOR_EMAIL_TECH . config('app.domain');
    }

    /**
     *
     *
     * @param string $username
     * @param string $file
     * @param string $text
     * @return string
     */
    public static function getDirectDownloadLink(string $hostname, string $file, ?string $description = null): string
    {
        $args = [
            'link' => strtolower($hostname) . "/" . UserData::MEDIA_DIRECT_DIR . "/" . $file,
        ];

        if (!$description || strlen($description) < 1) {
            $description = trans('feeds.text_download_file_directly');
        }

        foreach($args as $key => $value) {
            $description = str_replace("%$key%", $value, $description);
        }

        return $description;
    }

    /**
     *
     * @return array
     */
    private function getExport(): array
    {
        $username = $this->username;
        $feedId = $this->feedId;
        $feed = Feed::whereUsername($username)->whereFeedId($feedId)->firstOrFail();
        $aFeed = $feed->toArray();

        // Delete accounts which use this symbol (Corean signs) right away - SPAM protection
        if (isset($aFeed['rss']['description']) && mb_stristr($aFeed['rss']['description'], '샵')) {
            User::whereUsername($username)->forceDelete();
            exit();
        }

        // F*cking itunes categories need special handling
        $aItunesCategories = $aFeed['itunes']['category'] ?? [self::ITUNES_CATEGORY_DEFAULT];
        unset($aFeed['itunes']['category']);

        /**
         * array(array('main' => 'main category',
         *             'sub' => 'sub category' // optionnal
         *            ),
         *       // up to 3 rows
         *      )
         */
        foreach ($aItunesCategories as $key => $category) {
            if (!is_array($category)) {
                if (strstr($category, ":")) {
                    // Sub categories are written as main:sub in select list - now split them apart
                    list($main, $sub) = explode(":", $category);
                    $aFeed['itunes']['category'][] = ['main' => $main, 'sub' => $sub];
                } elseif (strlen($category) > 0) {
                    $aFeed['itunes']['category'][] = ['main' => $category];
                }
            }
        }

        if (isset($aFeed['itunes']['new-feed-url'])
            && strlen($aFeed['itunes']['new-feed-url']) < 1) {
            unset($aFeed['itunes']['new-feed-url']);
        }

        $aExport = $aFeed['rss'];

        if (isset($aFeed['entries']) && $aFeed['entries'] > 0) {
            $aExport['entries'] = (array)$aFeed['entries'];
            reset($aExport['entries']);

            foreach ($aExport['entries'] as $key => &$aEntry) {

                // Skip scheduled entries, drafts and those with false data
                if (!isset($aEntry['is_public'])
                    || in_array((int)$aEntry['is_public'], [Show::PUBLISH_DRAFT])
                    || !isset($aEntry['lastUpdate'])
                    || $aEntry['lastUpdate'] > time()) {
                    unset($aFeed['entries'][$key]);
                    continue;
                }

                if (isset($aEntry['show_media'])) {
                    $showMedia = $aEntry['show_media'];
                    unset($aEntry['show_media']);

                    if (!empty($showMedia)) {

                        $file = get_file($username, $showMedia);

                        if (!$file) {
                            // This should never happen...
                            continue;
                        }

                        $mimeType = $file['mimetype'];

                        if ($mimeType == 'audio/mp3') {
                            $mimeType = 'audio/mpeg'; // Hack: Otherwise Spotify show detection does not work
                        }

                        $aEntry['enclosure']    = [
                            [
                                'url'       => get_enclosure_uri($feedId, $file['name'], $aFeed['domain']), // TODO: , null , 'feed'
                                'length'    => $file['byte'],
                                'type'      => $mimeType,
                            ],
                        ];
                    }
                }
                // Ist: http://digitalesleben.podcaster.de/livinglinux/living-linux--5-:-viel-------hm--kein--wort-ping-pong-/
                // Soll: http://digitalesleben.podcaster.de/livinglinux/living-linux-5-viel-aeaeaehm-kein-wort-ping-pong/
                if (!isset($aEntry['link']) || empty($aEntry['link'])) {
                    $aEntry['link'] = get_link(strtolower($feedId), $aFeed['domain']['hostname'], null)
                        . '/' . $this->getBlogTitle($aEntry['title']) . '/';
                }

                if (isset($aEntry['itunes']['logo']) && $aEntry['itunes']['logo']) {
                    $mediaId = $aEntry['itunes']['logo'];
                    unset($aEntry['itunes']['logo']);
                    try {
                        $mediaUrl = $this->export($username, $feedId, $mediaId, UserData::LOGOS_PUBLIC_DIR);

                        // Should be an url by now
                        if (is_numeric($mediaUrl) || empty($mediaUrl)) {
                            Log::critical("ERROR 634: User '{$username}': iTunes Logo export for feed '{$feedId}' and media '{$mediaId}' is '{$mediaUrl}'.");
                            unset($aEntry['itunes']['logo']);
                        } else if ($mediaUrl) {
                            $aEntry['itunes']['logo'] = $mediaUrl;
                        }
                    } catch (\Exception $e) {
                        Log::critical("ERROR 640: User '{$username}': Getting iTunes Logo export for feed '{$feedId}' and media '{$mediaId}'.");
                        Log::critical($e->getTraceAsString());
                    } catch (\Throwable $e) {
                        Log::critical("ERROR 643: User '{$username}': Failed getting iTunes Logo export for feed '{$feedId}' and media '{$mediaId}'.");
                        Log::critical($e->getTraceAsString());
                    }
                }
            }

            // Sort entries by date
            uasort($aExport['entries'], function ($a, $b) {
                if ($a['lastUpdate'] == $b['lastUpdate']) {
                    return 0;
                }
                return ($a['lastUpdate'] > $b['lastUpdate']) ? -1 : 1;
            });
        }

        // Use RSS logo if available (deprecated)
        if (isset($aFeed['logo']['rss']) && $aFeed['logo']['rss']) {
            $aExport['image'] = $this->export($username, $feedId, $aFeed['logo']['rss'],
                UserData::LOGOS_PUBLIC_DIR);
        }

        $aExport['itunes'] = $aFeed['itunes'] ?? [];
        $aExport['googleplay'] = $aFeed['googleplay'] ?? [];

        if (isset($aFeed['logo']) && isset($aFeed['logo']['itunes']) && $aFeed['logo']['itunes']) {
            $aExport['itunes']['image'] = $this->export($username, $feedId, $aFeed['logo']['itunes'],
                UserData::LOGOS_PUBLIC_DIR);
        }

        // Set default logo if it is not set
        if (!isset($aExport['image']) || empty($aExport['image'])) {
            $aExport['image'] = $aExport['itunes']['image'] ?? null;
        }

        return ['data' => (array)$aExport, 'settings' => $feed->settings];
    }

    /**
     * @param  string  $title
     * @return string
     */
    public function getBlogTitle(string $title): string
    {
        $oldLocale = setlocale(LC_ALL, 0);

        if ($oldLocale == 'C') {
            setlocale(LC_ALL, "de_DE.UTF-8", 'de_DE@euro', 'de_DE', 'deu_deu');
        }
        $title = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
        $title = Str::lower(preg_replace('|-{2,}|', '-', preg_replace('|[^\d\w]|', '-', $title)));
        //$title = Str::lower(preg_replace('|-{2,}|', '-', preg_replace('|[^a-zA-Z0-9_]|', '-', $title)));
        $title = trim($title, '-');

        return $title;
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @param $mediaId
     * @param  string  $publicDir
     * @return string
     * @throws \Exception
     */
    public function export(string $username, string $feedId, $mediaId, string $publicDir = UserData::MEDIA_PUBLIC_DIR): string
    {
        if (!$mediaId) return false;

        $file = get_file($username, $mediaId);

        if (!$file) return false;

        $filename = $file['name'];
        try {
            $this->link($file, $publicDir);
        } catch (\Exception $e) {
            Log::critical("ERROR: User '{$username}': Failed linking {$mediaId} -> '{$file['name']}' for feed '{$feedId}'");
            Log::critical($e->getTraceAsString());
        }
        $aDomain = $this->getDomain($username, $feedId);

        return get_link($feedId . DIRECTORY_SEPARATOR . $publicDir . DIRECTORY_SEPARATOR
            . $filename, $aDomain['hostname'], null);
    }

    /**
     * @param  array  $file
     * @param  string  $publicDir
     * @return bool
     * @throws \Exception
     */
    public function link(array $file, string $publicDir): bool
    {
        $link = get_publish_path($this->username, $file['name'], $publicDir, $this->feedId);

        // Replace release number with /current/
        $link = preg_replace('#/releases/\d+/#', '/current/', $link);

        // If it already exists it´s fine just exit
        if (is_link($link) && readlink($link) == $file['path']) {
            return true;
        } elseif (is_link($link) && readlink($link) != $file['path']) {
            // If symlink points to a target with a different name try to delete it
            try {
                unlink($link);
            } catch (\Exception $e) {
                Log::debug("User `{$this->username}`: ERROR: Unlinking '{$link}' failed with: " . $e->getMessage());
            }
        }

        $path = dirname($link);
        // Ensure directory structure is existing
        if (!is_dir($path)) {
            if (!mkDir($path, 0755, true)) {
                throw new \Exception("User '{$this->username}': ERROR: Could not create directory \"$path\" or parent directory.");
            }
        }

        $target = preg_replace('#/releases/\d+/#', '/current/', $file['path']);
        //Log::debug("User '{$this->username}': Linking to '{$target}' from '{$link}'.");

        return symlink($target, $link); # softlink
    }

    /**
     * @deprecated
     * @param  string  $username
     * @throws \Exception
     */
    public function ensureFeedPath(string $username)
    {
        $feedPath = storage_path(get_user_feed_path($username));
        // Ensure directory structure is existing
        if (!is_dir($feedPath)) {
            Log::critical("User: '$username': Feed directory '$feedPath' does not exist.");
            if (!mkDir($feedPath, 0755, true)) {
                Log::critical("ERROR: User: '$username': Feed directory $feedPath could not be created.");
                throw new \Exception("ERROR: Could not create directory \"$feedPath\" or parent directory.");
            }
        }
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function publish()
    {
        $username = $this->username;
        $feedId = $this->feedId;
/*        if (isset($feed->settings['protection'])
            && $feed->settings['protection'] == 1) {
            $feedId = $feed->settings['protection_id'];
        }*/
        $feedPath = storage_path(get_user_feed_path($username));
        File::ensureDirectoryExists($feedPath);
        $res = file_put_contents($feedPath . DIRECTORY_SEPARATOR . $feedId . '.rss', $this->save());

        if (!$res) {
            Log::critical("ERROR: User: '$username': Could not publish feed '$feedId'.");
            throw new \Exception(trans('feeds.error_generator_feed_create_failed'));
        }

        return $res;
    }

    private function writeFirstLine(string $filename)
    {
        $xsltLink = config('app.url');
        $xsltStyles = config('app.url');
        $fileContents = file($filename);
        // Add the new line to the beginning
        array_unshift($fileContents, $xsltLink);
        // Write the file back
/*        $newContent = implode(PHP_EOL, $fileContents);

        $fp = fopen($filename, "w+");   // w+ means create new or replace the old file-content
        fputs($fp, $newContent);
        fclose($fp);*/

        file_put_contents($filename, implode(PHP_EOL, $fileContents));
    }

}
