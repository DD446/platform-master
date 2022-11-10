<?php

namespace App\Jobs;

use App\Events\WebserverReloadedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use TitasGailius\Terminal\Terminal;

class ReloadWebserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $feedId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Terminal::run('/usr/sbin/nginx -t');
        Log::debug("Checking webserver config");

        if ($response->ok()) {
            Log::debug("Reloading webserver");
            $response = Terminal::run('systemctl reload nginx');

            if (!$response->successful()) {
                foreach($response->lines() as $line) {
                    Log::debug("Could not restart webserver: " . $line);
                }
            } else {
                Log::debug("Webserver reloaded");
                event(new WebserverReloadedEvent($this->feedId));
            }
        }
    }
}
