<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RemoveWebsiteEntriesForYV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:remove-website-entries-yv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes url entries for shows by pattern';

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
     * @return int
     */
    public function handle()
    {
        $username = 'jkv3wg';
        $feeds = Feed::whereUsername($username)->get();
        $hasChanges = false;

        foreach ($feeds as $feed) {
            $entries = $feed->entries;

            if ($entries) {
                foreach ($entries as &$entry) {
                    if (isset($entry['link'])) {
                        if (Str::contains($entry['link'], 'podspot.de')) {
                            $this->line("Match: ".$entry['link']);
                            unset($entry['link']);
                            $hasChanges = true;
                        }
                    }
                }

                if ($hasChanges && $entries) {
                    $feed->whereUsername($feed->username)
                        ->whereFeedId($feed->feed_id)
                        ->update([
                            'entries' => array_values($entries)
                        ]);
                    refresh_feed($username, $feed->feed_id);
                }
            }
        }

        return Command::SUCCESS;
    }
}
