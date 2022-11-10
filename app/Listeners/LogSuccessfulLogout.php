<?php

namespace App\Listeners;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout implements ShouldQueue
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
     * @param  \Illuminate\Auth\Events\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        LaravelMatomoTracker::queueEvent('auth', 'logout', $event->user->username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
    }
}
