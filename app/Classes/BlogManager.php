<?php
/**
 * User: fabio
 * Date: 31.05.22
 * Time: 12:01
 */

namespace App\Classes;

use App\Models\Feed;
use App\Models\Show;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogManager
{

    /**
     * @var Feed
     */
    private $feed;

    /**
     * @param  Feed  $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * @param $aShow
     * @return int
     * @throws \Exception
     */
    public function update($aShow)
    {
        $feed = $this->feed;
        $feedId = $feed->feed_id;
        $username = $feed->username;
        $aDomain = $feed->domain;

        // Migrate to https
        if ($aDomain['protocol'] == 'http') {
            $aDomain['protocol'] = 'https';
            $hostname = $aDomain['protocol'] . '://' . $aDomain['subdomain'] . '.' . $aDomain['tld'];
            $aDomain['hostname'] = $hostname;
        } else {
            $hostname = $aDomain['hostname'];
        }

        Log::debug("User '$username': updating show `{$aShow['guid']}` with title `{$aShow['title']}` in feed '$feedId'. Using domain `{$hostname}`.");

        $description = $aShow['description'];

        if (isset($aShow['show_media'])
            && $aShow['show_media']
            && isset($aShow['addDownloadLink'])
            && $aShow['addDownloadLink'] == true) {
            $file = get_file($username, $aShow['show_media']);

            if ($file) {
                $description .= PHP_EOL.PHP_EOL.
                    FeedGeneratorManager::getDirectDownloadLink($hostname, $file['name'],
                        $aShow['downloadlink_description']);
            }
        }

        if (isset($aShow['sync_id']) && is_numeric($aShow['sync_id'])) {
            $aData  = [
                'action'    => 'update',
                'show'      => [
                    'post_content'      => $description,
                    'post_date'         => CarbonImmutable::createFromTimestamp($aShow['lastUpdate'])->format('Y-m-d H:i:s'),
                    'post_date_gmt'     => CarbonImmutable::createFromTimestamp($aShow['lastUpdate']-(60*60*2))->format('Y-m-d H:i:s'),
                    'post_name'         => $aShow['title'] ?? 'Podcast',
                    'post_title'        => $aShow['title'] ?? 'Podcast',
                    'post_status'       => ((int)$aShow['is_public'] == Show::PUBLISH_NOW) ? 'publish' : 'pending',
                    'post_type'         => strtolower($feedId),
                    'ID'                => $aShow['sync_id'] ?? '',
                    'author'            => $aShow['author'] ?? '',
                    'enclosure'         => $aShow['show_media'] ?
                        get_wordpress_enclosure($username, $aShow['show_media'], $feed) : null,
                    'copyright'         => $aShow['copyright'] ?? '',
                    'website'           => $aShow['link'] ?? config('app.url'),
                    'user'              => strtolower($username),
                    #'tags_input'       => $oShow->,
                ],
            ];
            $aData['domain'] = $aDomain['hostname'];

            return $this->post($aData, $username);
        } else {
            Log::debug("User '$username': updating show " . $aShow["title"] . " in feed $feedId failed. Adding new show now.");
            // A new show was passed - happens when a previous sync failed
            return $this->add($username, $aShow);
        }
    }

    /**
     * @param  string  $username
     * @param  array  $aShow
     * @return false|float|int|string
     * @throws \Exception
     */
    public function add(string $username, array $aShow)
    {
        $feed = $this->feed;
        $feedId = $feed->feed_id;

        Log::debug("User '{$username}': Adding show " . $aShow["title"] . " in feed `$feedId`");

        $aDomain = $feed->domain;
        $hostname = $aDomain['hostname'];
        $description = $aShow['description'];

        if (isset($aShow['show_media'])
            && $aShow['show_media']
            && isset($aShow['addDownloadLink'])
            && $aShow['addDownloadLink'] == true) {
            $file = get_file($username, $aShow['show_media']);
            if ($file) {
                $description .= PHP_EOL.PHP_EOL.
                    FeedGeneratorManager::getDirectDownloadLink($hostname, $file['name'],
                        $aShow['downloadlink_description']);
            }
        }

        $logoUrl = false;

        if (isset($aShow['itunes']) && $aShow['itunes']['logo']) {
            $mediaId = $aShow['itunes']['logo'];
            try {
                // TODO: Replace
                //$logoUrl = MediaDAO::singleton()->export($feedId, $mediaId, $username, MediaDAO::LOGOS_PUBLIC_DIR);
            } catch (\Exception $exception) {
                Log::error("ERROR: User: '{$username}': ");
            }
        }

        if (!isset($aShow['show_media'])) {
            return false;
        }

        $_aShow = [
            'post_content'      => $description,
            'post_date'         => CarbonImmutable::createFromTimestamp($aShow['lastUpdate'])->format('Y-m-d H:i:s'),
            'post_date_gmt'     => CarbonImmutable::createFromTimestamp($aShow['lastUpdate']-(60*60*2))->format('Y-m-d H:i:s'),
            'post_name'         => $aShow['title'] ?? 'Podcast',
            'post_title'        => $aShow['title'] ?? 'Podcast',
            'post_status'       => ((int)$aShow['is_public'] == Show::PUBLISH_NOW) ? 'publish' : 'pending',
            'post_type'         => strtolower($feedId),
            'author'            => $aShow['author'] ?? '',
            'enclosure'         => $aShow['show_media'] ?
                get_wordpress_enclosure($username, $aShow['show_media'], $feed) : null,
            'logourl'           => $logoUrl ?: null,
            'copyright'         => $aShow['copyright'] ?? '',
            'website'           => $aShow['link'] ?? config('app.url'),
            'user'              => strtolower($username),
            'comment_status'    => ((isset($aShow['inactiveComments']) && $aShow['inactiveComments'] == true) ? 'closed' : 'open'),
            'ping_status'       => ((isset($aShow['inactiveComments']) && $aShow['inactiveComments'] == true) ? 'closed' : 'open'),
        ];
        $_aShow = array_filter($_aShow);
        $aData  = [
            'action'    => 'add',
            'show'      => $_aShow,
            'domain'    => $aDomain['hostname']
        ];
        $postId = $this->post($aData, $username);

        if ($postId && is_numeric($postId)) {
            Log::debug("[User $username] Setting post id '$postId' for show with guid '{$aShow['guid']}'.");
            return $postId;
        } else {
            return false;
        }
    }

    /**
     * @param  array  $data
     * @param  string  $username
     * @return false|string
     */
    private function post(array $data, string $username)
    {
        $domain = $this->feed->domain['subdomain'] . '.' . $this->feed->domain['tld'];
        $url = $this->getBlogUrl($domain);
        $feedId = $this->feed->feed_id;
        $res = Http::asForm()->post($url, $data);

        if ($res->ok()) {
            return $res->body();
        }

        if ($res->failed()) {
            Log::debug("User: {$username}: Action {$data['action']} for blog entry failed for feed `{$feedId}` for show with message: " . $res->body());
        }

        return false;
    }

    /**
     * @param  string  $domain
     * @return string
     */
    private function getBlogUrl(string $domain): string
    {
        $uri = '/pod-admin/pod-entry.php';
        $url = $this->feed->domain['protocol'] . '://' . $domain . $uri;

        return $url;
    }
}
