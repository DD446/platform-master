<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\FeedRemovalEvent;

class FeedBlogRemovalListener
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
        \App\Jobs\DeleteBlogFeed::dispatchNow($event->username, $event->feedId, $event->domain);
    }
}
