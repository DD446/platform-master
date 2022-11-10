<?php

namespace App\Providers;

use App\Jobs\LogSearchQuery;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\UserQueue;
use App\Observers\UserObserver;
use App\Observers\UserQueueObserver;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\FeedUpdateEvent' => [
            'App\Listeners\PlayerConfigCacheUpdateListener',
        ],
        'App\Events\FeedRemovalEvent' => [
            // TODO: 'App\Listeners\FeedPodcastDeRemovalListener',
        ],
        'App\Events\ShowAddEvent' => [
            'App\Listeners\PlayerConfigCacheUpdateListener',
            'App\Listeners\TwitterSendTweetListener',
        ],
        \Illuminate\Console\Events\CommandStarting::class => [
            \App\Listeners\CommandStartingListener::class
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Twitter\\TwitterExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        UserQueue::observe(new UserQueueObserver);
        User::observe(new UserObserver);

        Event::listen('TeamTNT\Scout\Events\SearchPerformed', function($event)
        {
            LogSearchQuery::dispatch($event);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            $this->app->path('Listeners'),
        ];
    }
}
