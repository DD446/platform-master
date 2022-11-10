<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use Illuminate\Console\Command;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper command to run one-time stuff - be careful running this!';

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
        $contracts = \App\Models\AudiotakesContract::whereNotNull('feed_id')->get();

        foreach($contracts as $contract) {
            $this->line("Contract with Feed ID: " . $contract->feed_id);

            $feed = Feed::whereFeedId($contract->feed_id)->first();

            if ($feed) {
                $entries = $feed->entries;
                if ($entries) {
                    foreach ($entries as $entry) {
                        try {
                            $file = get_file($feed->username, $entry['show_media']);
                            if ($file) {
                                $entry['audiotakes_guid'] = sha1($file['name']);
                                $this->line("Feed ID: ".$feed->feed_id.', entry: '.$entry['guid'] . ', AID: ' . $entry['audiotakes_guid']);
                            }
                        } catch (\Exception $e) {
                        }
                    }

                    $feed->whereUsername($feed->username)
                        ->whereFeedId($feed->feed_id)
                        ->update([
                            'entries' => array_values($entries)
                        ]);
                }
            }

            refresh_feed($feed->username, $feed->feed_id);
        }
        return 0;
    }
}
