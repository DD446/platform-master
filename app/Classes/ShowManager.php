<?php
/**
 * User: fabio
 * Date: 09.11.20
 * Time: 20:20
 */

namespace App\Classes;


use App\Models\Feed;
use App\Models\Show;
use Illuminate\Support\Facades\Log;

class ShowManager
{
    /**
     * @param  Feed  $feed
     * @param  string  $guid
     * @return array
     * @throws \Exception
     */
    public function get(Feed $feed, string $guid): array
    {
        foreach($feed->entries as $key => $entry) {
            if (isset($entry['guid']) && $entry['guid'] && $entry['guid'] == $guid) {

                if (isset($entry['show_media'])
                    && !empty($entry['show_media'])) {

                    try {
                        $file = get_file($feed->username, $entry['show_media']);

                        if ($file) {
                            unset($file['path']);
                            $entry['media'] = $file;
                        }
                    } catch (\Exception $e) {
                    }
                }

                if (isset($entry['itunes']['logo'])
                    && !empty($entry['itunes']['logo'])) {

                    try {
                        $file = get_image_uri($feed->feed_id, $entry['itunes']['logo'], $feed->domain);

                        if ($file) {
                            $entry['logo'] = $file;
                        }
                    } catch (\Exception $e) {
                        Log::debug("User {$feed->username}: Feed `{$feed->feed_id}`: Getting image uri failed.");
                    }
                }

                return $entry;
            }
        }

        if (!isset($entry['guid']) || !$entry['guid']) {
            Log::debug("User {$feed->username}: Feed `{$feed->feed_id}`: Getting show failed because of missing GUID.");
        }

        throw new \Exception(trans('shows.exception_no_entry_found_for_guid', ['guid' => $guid]));
    }

    /**
     * @param  Feed  $feed
     * @param  string  $guid
     * @return bool
     */
    public function copy(Feed $feed, string $guid, ?Feed $feedTo = null): bool
    {
        $entries = $feed->entries;
        $_entry = $this->get($feed, $guid);
        $_entry['title'] .= trans('shows.text_duplication_copy');
        $_entry['guid'] = get_guid('pod-');
        $_entry['lastUpdate'] = time();
        $_entry['is_public'] = Show::PUBLISH_DRAFT;
        unset($_entry['sync_id']);

        if ($feedTo) {
            $entries = $feedTo->entries;
            $feed = $feedTo;
        }

        array_push($entries, $_entry);

        return $feed->whereUsername(auth()->user()->username)
            ->whereFeedId($feed->feed_id)
            ->update([
                'entries' => array_values($entries)
            ]);
    }

    /**
     * @param  Feed  $feed
     * @param  string  $guid
     * @return bool
     */
    public function delete(Feed $feed, string $guid): bool
    {
        $entries = $feed->entries;
        $foundEntry = false;

        foreach($entries as $key => &$entry) {
            if (!isset($entry['guid'])) {
                $entry['guid'] = get_guid('pod-');
                continue 1;
            }
            if ($entry['guid'] == $guid) {
                unset($entries[$key]);
                $foundEntry = true;
                break;
            }
        }

        if (!$foundEntry) {
            return false;
        }

        return $feed->whereUsername($feed->username)
            ->whereFeedId($feed->feed_id)
            ->update([
                'entries' => array_values($entries)
            ]);
    }

    /**
     * @param  Feed  $feed
     * @param  string  $guid
     * @param  Feed  $feedTo
     * @return bool
     * @throws \Exception
     */
    public function move(Feed $feed, string $guid, Feed $feedTo): bool
    {
        $_entry = $this->get($feed, $guid);

        $entries = $feedTo->entries;
        array_push($entries, $_entry);
        $res = $feedTo->whereUsername(auth()->user()->username)
            ->whereFeedId($feedTo->feed_id)
            ->update([
                'entries' => array_values($entries)
            ]);

        if (!$res) {
            throw new \Exception(trans('shows.error_message_move_show'));
        }

        return $this->delete($feed, $guid);
    }
}
