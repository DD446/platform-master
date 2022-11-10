<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\FileSavedEvent;
use App\Models\UserData;

class CreateMediaLinkListener implements ShouldQueue
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
    public function handle(FileSavedEvent $event)
    {
        $user = $event->user;
        $path = $event->file['path'];

        if (!$user->link($path,UserData::MEDIA_DIRECT_DIR)) {
            Log::error("User `{$user->username}`: Creating link for `{$path}` failed.");
        }
    }
}
