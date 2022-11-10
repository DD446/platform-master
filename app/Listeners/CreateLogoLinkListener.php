<?php

namespace App\Listeners;

use App\Classes\FeedGeneratorManager;
use App\Events\LogoSavedEvent;
use App\Models\UserData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateLogoLinkListener
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
    public function handle(LogoSavedEvent $event)
    {
        $username = $event->user->username;
        $feedId = $event->feedId;
        $fgm = new FeedGeneratorManager($username, $feedId);
        $fgm->link($event->file, UserData::LOGOS_PUBLIC_DIR);
    }
}
