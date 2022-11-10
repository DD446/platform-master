<?php

namespace App\Jobs;

use App\Classes\Statistics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StatsExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected \App\Models\StatsExport $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\Models\StatsExport $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("User " . $this->export->user->username . ": Exporting statistics job number: " . $this->export->id);

        $statistics = new Statistics();

        if (!$statistics->getExport($this->export)) {
            Log::error(trans('stats.error_get_export'));
            throw new \Exception(trans('stats.error_get_export'));
        }
    }
}
