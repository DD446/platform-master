<?php

namespace App\Listeners;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin implements ShouldQueue
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
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        LaravelMatomoTracker::queueEvent('auth', 'login', $event->user->username ,\Carbon\CarbonImmutable::now()->toFormattedDateString());

        //LaravelMatomoTracker::queueEvent('auth', 'login', 'beispiel', \Carbon\CarbonImmutable::now()->toFormattedDateString());
    }
}
