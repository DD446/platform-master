<?php

namespace App\Listeners;

use App\Events\StatsExportEvent;
use App\Jobs\StatsExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StatsExportListener implements ShouldQueue
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
     * @param  StatsExportEvent  $event
     * @return void
     */
    public function handle(StatsExportEvent $event)
    {
        StatsExport::dispatch($event->export);
    }
}
