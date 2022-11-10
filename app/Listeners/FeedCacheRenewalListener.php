<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\FeedUpdateEvent;
use Illuminate\Support\Facades\Log;

class FeedCacheRenewalListener implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(FeedUpdateEvent $event)
    {
        Log::debug("User '{$event->username}': FeedCacheRenewalListener called from FeedUpdateEvent for feed '{$event->feedId}'.");

        refresh_feed($event->username, $event->feedId);
    }
}
