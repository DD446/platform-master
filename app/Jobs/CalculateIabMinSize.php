<?php

namespace App\Jobs;

use App\Classes\Statistics;
use App\Models\UserUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateIabMinSize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserUpload $userUpload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserUpload $userUpload)
    {
        $this->userUpload = $userUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stats = new Statistics();
        $userUpload = $this->userUpload;
        $username = $userUpload->user->username;
        $file = get_file($username, $userUpload->file_id);

        if ($userUpload && !$userUpload->iab_min_size && $file) {
            $fileSizeOfFirstMinute = $stats->getFileSizeOfFirstMinute($file['path']);

            if ($fileSizeOfFirstMinute !== false) {
                if ($userUpload
                    ->update([
                        'iab_min_size' => $fileSizeOfFirstMinute
                    ])) {
                    Log::debug("User $username: Updated IAB min size $fileSizeOfFirstMinute for file ".$file["name"]);
                }
            }
        }
    }
}
