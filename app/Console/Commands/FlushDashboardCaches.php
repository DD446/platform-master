<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushDashboardCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:flush-dashboard-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empties cached statistic data for users´ dashboards';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $yesterdayFormatted = (new \DateTimeImmutable('yesterday'))->format('d-M-Y');
        $theDayBeforeYesterdayFormatted = (new \DateTimeImmutable('-2 days'))->format('d-M-Y');
        Cache::tags([
            'listeners_' . $yesterdayFormatted,
            'listeners_' . $theDayBeforeYesterdayFormatted,
            'subscribers_' . $yesterdayFormatted,
            'subscribers_' . $theDayBeforeYesterdayFormatted,
        ])->flush();
    }
}
