<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RemoveWebsiteEntriesForAA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:remove-website-entries-aa';

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
        $username = 'ofietj';
        $feeds = Feed::whereUsername($username)->get();

        foreach ($feeds as $feed) {
            $entries = $feed->entries;
            $hasChanges = false;

            if ($entries) {
                foreach ($entries as &$entry) {
                    if (isset($entry['link'])) {
                        if (Str::contains($entry['link'], 'podigee.io')) {
                            $this->line("Match: ".$entry['link']);
                            unset($entry['link']);
                            $hasChanges = true;
                        }
                    }
                }

                if ($hasChanges && $entries) {
                    $feed->whereUsername($username)
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
