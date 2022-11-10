<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Events\CachePlayerConfig;

class CachePlayerConfigListener
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
    public function handle(CachePlayerConfig $event)
    {
        Log::debug("Listener: CachePlayerConfigListener");

        $pc = $event->playerConfig;
        cache_player_config($pc);
    }
}
