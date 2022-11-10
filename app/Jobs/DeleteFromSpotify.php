<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use podcasthosting\PodcastClientSpotify\Delivery\Client;

class DeleteFromSpotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $spotifyUri;

    /**
     * Create a new job instance.
     *
     * @param  string  $spotifyUri
     */
    public function __construct(string $spotifyUri)
    {
        $this->spotifyUri = $spotifyUri;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!is_null($this->spotifyUri)) {
            $dc = new Client(config('services.spotify.token'));
            try {
                if ($dc->remove($this->spotifyUri)) {
                    Log::debug("Successfully removed entry `{$this->spotifyUri}` from Spotify.");
                }
            } catch (\Exception $e) {
                Log::error("Removing Spotify URI {$this->spotifyUri} failed: " . $e->getMessage());
            }
        }
    }
}
