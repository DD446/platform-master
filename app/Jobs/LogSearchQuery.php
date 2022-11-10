<?php

namespace App\Jobs;

use App\Models\SearchLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TeamTNT\Scout\Events\SearchPerformed;

class LogSearchQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SearchPerformed $searchPerformed;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SearchPerformed $searchPerformed)
    {

        $this->searchPerformed = $searchPerformed;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $a = (array)$this->searchPerformed;

        if (SearchLog::where('query', '=', $a['query'])->count() < 1) {
            (new SearchLog($a))->save();
        }
    }
}
