<?php

namespace App\Jobs;

use App\Classes\BlogManager;
use App\Classes\FeedGeneratorManager;
use App\Models\Show;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Classes\FeedWriterLegacy;
use App\Models\Feed;

class SyncShowsToBlog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Feed
     */
    private $feed;

    /**
     * Create a new job instance.
     *
     * @param  Feed  $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!isset($this->feed->entries)) {
            return;
        }

        Log::debug("User: {$this->feed->username}. Syncing " . count($this->feed->entries) . " shows to blog...");

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

        if ($hasChanges) {
            $this->feed->whereUsername($username)->whereFeedId($feedId)->update(['entries' => $entries]);
        }
    }
}
