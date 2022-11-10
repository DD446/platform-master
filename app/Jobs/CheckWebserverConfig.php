<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WebserverConfigError;

class CheckWebserverConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("Checking nginx");

        $ret = exec("/usr/sbin/nginx -t >> /dev/null 2>&1", $output, $status);

        if ($status > 0) {
            Notification::route('mail', 'fabio@podcaster.de')
                ->route('mail', 'fabio.bacigalupo@gmail.com')
                ->notify(new WebserverConfigError($output, $status, $ret));
        }
    }
}
