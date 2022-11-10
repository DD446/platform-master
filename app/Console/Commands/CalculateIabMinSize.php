<?php

namespace App\Console\Commands;

use App\Classes\Statistics;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\UserUpload;
use Illuminate\Console\Command;

class CalculateIabMinSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:calculate-iab-min-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves file size of first 60 seconds of media file';

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
        $stats = new Statistics();

        $contracts = AudiotakesContract::signed()->get();

        foreach($contracts as $contract) {
            $user = $contract->user;

            if ($user) {
                $username = $user->username;
                $this->line("User $username: Contract: " . $contract->identifier);
                $feed = Feed::whereUsername($username)->where('feed_id', '=', $contract->feed_id)->first();

                if ($feed && $feed->entries) {
                    $this->line("User $username: Processing feed: " . $contract->feed_id . " with " . count($feed->entries) . " entries.");

                    foreach($feed->entries as $show) {
                        if (!isset($show['show_media'])) {
                            continue;
                        }
                        $fileId = $show['show_media'];

                        try {
                            $file = get_file($username, $fileId);
                            $userUpload = UserUpload::where('user_id', '=', $contract->user_id)->where('file_id', '=', $fileId)->first();

                            if ($userUpload && !$userUpload->iab_min_size) {
                                $fileSizeOfFirstMinute = $stats->getFileSizeOfFirstMinute($file['path']);

                                if ($fileSizeOfFirstMinute !== false) {
                                    if ($userUpload
                                        ->update([
                                            'iab_min_size' => $fileSizeOfFirstMinute
                                        ])) {
                                        $this->line("User $username: Updated IAB min size $fileSizeOfFirstMinute for file ".$file["name"]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $this->error("ERROR: User $username: Getting file with id $fileId");
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
