<?php

namespace App\Console\Commands;

use App\Models\Feed;
use Illuminate\Console\Command;

class AddMissingAudiotakesIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:add-missing-audiotakes-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $contracts = \App\Models\AudiotakesContract::signed()->get();

        foreach($contracts as $contract) {
            $this->line("Contract `{$contract->identifier}` for feed: " . $contract->feed_id);
            $user = $contract->user;

            if ($user) {
                $feed = Feed::where('username', '=', $user->username)->whereFeedId($contract->feed_id)->first();
                if ($feed) {
                    $entries = $feed->entries;
                    if ($entries) {
                        foreach ($entries as &$entry) {
                            try {
                                if (isset($entry['show_media']) && !isset($entry['audiotakes_guid'])) {
                                    $file = get_file($feed->username, $entry['show_media']);
                                    if ($file) {
                                        $entry['audiotakes_guid'] = sha1($file['name']);
                                        $this->line("Feed ID: ".$feed->feed_id.', entry: '.$entry['guid'] . ', AID: ' . $entry['audiotakes_guid']);
                                    }
                                }
                            } catch (\Exception $e) {
                                $this->error($e->getMessage());
                            }
                        }

                        $feed->whereUsername($feed->username)
                            ->whereFeedId($feed->feed_id)
                            ->update([
                                'entries' => array_values($entries)
                            ]);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
