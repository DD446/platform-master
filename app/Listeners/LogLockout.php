<?php

namespace App\Listeners;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLockout implements ShouldQueue
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
     * @param  \Illuminate\Auth\Events\Lockout  $event
     * @return void
     */
    public function handle(Lockout $event)
    {
        LaravelMatomoTracker::queueEvent('auth', 'lockout', $event->request->url(), \Carbon\CarbonImmutable::now()->toFormattedDateString());
    }
}
