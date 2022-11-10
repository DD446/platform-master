<?php

namespace App\Listeners;

use App\Events\CachePlayerConfig;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\FeedUpdateEvent;
use App\Models\PlayerConfig;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PlayerConfigCacheUpdateListener implements ShouldQueue
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
     * @param  FeedUpdateEvent  $event
     * @return void
     */
    public function handle($event)
    {
        Log::debug("User '{$event->username}': Called: 'PlayerConfigCacheUpdateListener'");

        $user = User::whereUsername($event->username)->select('usr_id')->first();

        if ($user) {
            $configs = PlayerConfig::where('user_id', '=', $user->user_id)
                ->where('player_configurable_id', '=', $event->feedId)
                ->orWhere('feed_id', '=', $event->feedId)->get();
            foreach ($configs as $config) {
                event(new CachePlayerConfig($config));
            }
        }
    }
}
