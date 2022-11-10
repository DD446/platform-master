<?php

namespace App\Listeners;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;

class LogAuthenticationAttempt implements ShouldQueue
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
     * @param  \Illuminate\Auth\Events\Attempting  $event
     * @return void
     */
    public function handle(Attempting $event)
    {
        LaravelMatomoTracker::queueEvent('auth', 'attempt', Arr::first($event->credentials), \Carbon\CarbonImmutable::now()->toFormattedDateString());
    }
}
