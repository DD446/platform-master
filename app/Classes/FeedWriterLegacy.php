<?php
/**
 * User: fabio
 * Date: 04.05.19
 * Time: 08:58
 */

namespace App\Classes;

use Illuminate\Support\Facades\Log;
use App\Models\Feed;

class FeedWriterLegacy extends LegacyBase
{
    /**
     * FeedWriterLegacy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->rootDir . '/modules/podcaster/classes/FeedDAO.php';
        require_once $this->rootDir . '/lib/podcaster/SyncShowDAO.php';
    }

    public function write(string $username, string $feedId)
    {
        \FeedDAO::singleton()->publish($feedId, $username);
    }

    public function setLogo(string $username, string $feedId, int $mediaId = null)
    {
        \FeedDAO::singleton()->setLogo($feedId, $username, 'itunes', $mediaId);
    }

    /**
     * @return mixed
     */
    public function publishScheduledPosts()
    {
        return \FeedDAO::singleton()->publishScheduledPosts();
    }

    /**
     * Deletes a feed and all its references
     *
     * @param string $feedId
     * @param string $username
     * @return bool
     * @throws Exception
     */
    public function delete(string $username, string $feedId)
    {
        return \FeedDAO::singleton()->delete($username, $feedId);
    }

    /**
     * @param  Feed  $feed
     * @return mixed
     */
    public function syncShows(Feed $feed)
    {
        Log::debug("User: {$feed->username}: Feed-ID {$feed->feed_id}: Syncing " . count($feed->entries) . " shows.");

        $oSyncShow = new \SyncShowDAO();

        return $oSyncShow->updateAll($feed->username, (array)$feed->entries, $feed->feed_id);
    }
}
