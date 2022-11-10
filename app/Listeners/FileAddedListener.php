<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use App\Events\FileSavedEvent;

class FileAddedListener
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
    public function handle(FileSavedEvent $event)
    {
        $user = $event->user;
        $file = $event->file;

        if (!$event->isImport) {
            \App\Jobs\SubstractSpace::dispatch($user, $file);
        }
    }
}
