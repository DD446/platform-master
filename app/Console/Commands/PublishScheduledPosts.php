<?php

namespace App\Console\Commands;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Jobs\SyncShowToBlog;
use App\Models\Feed;
use App\Models\Show;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Classes\FeedWriterLegacy;
use App\Events\ShowAddEvent;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:publish-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Veröffentlicht geplante Beiträge';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug("Scheduler: Publish posts");
        $this->line("Publishing scheduled posts");

        $feeds = Feed::select(['username', 'feed_id', 'entries'])
            ->where('entries', 'elemMatch', [
            '$and' => [
                [
                    '$or' => [
                        ["is_public" => Show::PUBLISH_FUTURE],
                        ["is_public" => "" .  Show::PUBLISH_FUTURE]
                    ]
                ],
                [
                    '$or' => [
                        ["lastUpdate" => ['$lte' => time()]],
                        ["lastUpdate" => ['$lte' => "" . time()]]
                    ]
                ]
            ]
        ])
        ->cursor();
/*        ->update([
            'entries.$.is_public' => Show::PUBLISH_NOW
        ]);*/

        foreach ($feeds as $feed) {
            $refreshed = [];
            $shows = $feed->entries;
            foreach ($shows as &$show) {
                if ($show['is_public'] == Show::PUBLISH_FUTURE) {
                    if ($show['lastUpdate'] <= time()) {
                        $show['is_public'] = Show::PUBLISH_NOW;
                        $refreshed[] = $show['guid'];
                    }
                }
            }

            try {
                if ($feed->whereUsername($feed->username)
                    ->whereFeedId($feed->feed_id)
                    ->update([
                        'entries' => array_values($shows)
                    ])) {
                    foreach ($refreshed as $guid) {
                        Log::debug("User {$feed->username}: Publish scheduled post: Feed-ID {$feed->feed_id}: Found GUID {$guid}");
                        event(new ShowAddEvent($feed->username, $feed->feed_id, $guid));
                        SyncShowToBlog::dispatch($feed, $guid);
                        LaravelMatomoTracker::queueEvent('show', 'published', $feed->username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
                    }
                    refresh_feed($feed->username, $feed->feed_id);
                }
            } catch (\Exception $e) {
                Log::error("User {$feed->username}: Failed to update feed {$feed->feed_id} for scheduled post {$guid}: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }
    }
}
