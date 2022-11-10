<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use App\Events\FeedRemovalEvent;

class FeedLinksRemovalListener
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
     * @throws \Exception
     */
    public function handle(FeedRemovalEvent $event)
    {
        // CAUTION: Prevent deleteing whole public directory including media download links
        if (!$event->feedId) {
            throw new \Exception('No feed id provided');
        }

        $dir = get_user_public_path($event->username) . DIRECTORY_SEPARATOR . $event->feedId;

        if (File::exists($dir)) {
            File::deleteDirectories($dir);
        }
    }
}
