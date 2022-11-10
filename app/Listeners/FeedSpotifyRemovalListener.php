<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Events\FeedRemovalEvent;
use App\Jobs\DeleteFromSpotify;

class FeedSpotifyRemovalListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FeedRemovalEvent $event)
    {
        Log::debug("User `{$event->username}`: Received FeedRemovalEvent to delete Spotify entry for feed `{$event->feedId}`");

        if (!is_null($event->spotifyUri)) {
            try {
                DeleteFromSpotify::dispatch($event->spotifyUri);
            } catch (\Exception $e) {
                Log::error("User `{$event->username}`: Removing Spotify entry for feed `{$event->feedId}` failed: " . $e->getMessage());
            }
        }
    }
}
