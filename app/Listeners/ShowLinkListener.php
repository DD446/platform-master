<?php

namespace App\Listeners;

use App\Events\ShowAddEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Feed;
use Illuminate\Support\Facades\Log;

class ShowLinkListener implements ShouldQueue
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
     * @throws \Exception
     */
    public function handle(ShowAddEvent $event)
    {
        $username = $event->username;
        $guid = $event->guid;
        $feed = Feed::where('username', '=', $username)->where('feed_id', '=', $event->feedId)->firstOrFail();

        Log::debug("User {$username}: Called 'ShowLinkListener' for feed '{$feed->feed_id}' and show with guid '{$guid}'.");

        link_show_for_feed($username, $feed, $guid);
    }
}
