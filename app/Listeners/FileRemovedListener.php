<?php

namespace App\Listeners;

use App\Events\FileDeletedEvent;

class FileRemovedListener
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
    public function handle(FileDeletedEvent $event)
    {
        $user = $event->user;
        $file = $event->file;

        \App\Jobs\AddSpace::dispatch($user, $file);
    }
}
