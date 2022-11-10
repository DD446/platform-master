<?php

namespace App\Console\Commands;

use App\Classes\Statistics;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\UserUpload;
use Illuminate\Console\Command;

class CalculateIabMinSizeForAllUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:calculate-iab-min-size-for-all-uploads';

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
        $uploads = UserUpload::with(['user'])->whereNull('iab_min_size')->cursor();

        foreach ($uploads as $upload) {
            if ($upload->user) {
                $username = $upload->user->username;
                $fileId = $upload->file_id;
                $this->line("User $username");
                try {
                    $file = get_file($username, $fileId);
                    $userUpload = UserUpload::where('user_id', '=', $upload->user->id)->where('file_id', '=', $fileId)->first();

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

        return Command::SUCCESS;
    }
}
