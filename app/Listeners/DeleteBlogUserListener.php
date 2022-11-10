<?php

namespace App\Listeners;

use App\Events\UserDeletedEvent;
use App\Jobs\DeleteBlogUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteBlogUserListener
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
     * @param  \App\Events\UserDeletedEvent  $event
     * @return void
     */
    public function handle(UserDeletedEvent $event)
    {
        $username = $event->user->username;

        DeleteBlogUser::dispatch($username);
    }
}
