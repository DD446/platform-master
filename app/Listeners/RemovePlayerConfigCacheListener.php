<?php

namespace App\Listeners;

use Illuminate\Support\Facades\File;
use App\Events\RemovePlayerConfigCache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemovePlayerConfigCacheListener implements ShouldQueue
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
     * @param  RemovePlayerConfigCache  $event
     * @return void
     */
    public function handle(RemovePlayerConfigCache $event)
    {
        $pc = $event->playerConfig;
        $file = storage_path('app/public/player/config/' . $pc->uuid . '.js');
        File::delete($file);
    }
}
