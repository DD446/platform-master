<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use App\Classes\FeedWriterLegacy;
use App\Events\FeedRemovalEvent;
use App\Models\Feed;

class FeedRemovalListener
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
     * @param  FeedRemovalEvent  $event
     * @return void
     * @throws \Exception
     */
    public function handle(FeedRemovalEvent $event)
    {
        // Statische Datei löschen
        $feedFile = get_user_feed_filename($event->username, $event->feedId);

        if (File::exists($feedFile)) {
            File::delete($feedFile);
        }
    }
}
