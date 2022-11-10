<?php

namespace App\Jobs;

use App\Classes\BlogManager;
use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncShowToBlog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Feed
     */
    private $feed;

    /**
     * @var string
     */
    private $guid;

    /**
     * Create a new job instance.
     *
     * @param  Feed  $feed
     * @param  string  $guid
     */
    public function __construct(Feed $feed, string $guid)
    {
        $this->feed = $feed;
        $this->guid = $guid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("User: {$this->feed->username}. Syncing show `{$this->guid}` to blog...");

        $feedId = $this->feed->feed_id;
        $username = $this->feed->username;

        // Do not try to sync if website is a redirect (external)
        if (isset($this->feed->domain['website_redirect']) && $this->feed->domain['website_redirect']) {
            Log::debug("User '$username': Syncing shows for feed '$feedId'. Aborted. Found a redirect to `{$this->feed->domain['website_redirect']}`.");
            return;
        }

        if (!isset($this->feed->domain) || !$this->feed->domain) {
            Log::debug("User '$username': Syncing shows for feed '$feedId'. Aborted. Found no domain entry at all.");
            return;
        }

        if (!isset($this->feed->domain['website_type']) || !$this->feed->domain['website_type']) {
            Log::debug("User '$username': Syncing shows for feed '$feedId'. Aborted. Found no website type.");
            return;
        }

        $entries = $this->feed->entries;
        $hasChanges = false;
        $bm = new BlogManager($this->feed);

        foreach ($entries as &$aShow) {
            // Find the correct show
            if ($this->guid == $aShow['guid']) {

                $_res = $bm->update($aShow);

                if (!$_res) {
                    Log::error("ERROR: User '$username': Updating show with GUID " . $aShow['guid'] . " returned no sync id.");
                } elseif (isset($aShow['sync_id']) && is_numeric($_res) && $aShow['sync_id'] != $_res) {
                    // Happens if updating failed and ID was reset
                    $aShow['sync_id'] = $_res;
                    $hasChanges = true;
                } elseif (!isset($aShow['sync_id']) && is_numeric($_res)) {
                    $aShow['sync_id'] = $_res;
                    $hasChanges = true;
                }
            }
        }

        if ($hasChanges) {
            $this->feed->whereUsername($username)->whereFeedId($feedId)->update(['entries' => $entries]);
        }
    }
}
