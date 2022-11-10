<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Classes\StatisticsLegacy;
use App\Models\Feed;

class PublishFrequencyStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:publish-frequency-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts information about publish frequency across all feeds';

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
        $frequencyHistogramGoogleChartDataFile = storage_path('frequency_histogram.txt');
        $frequencySubscribersTrendlineGoogleChartDataFile = storage_path('frequency_subscribers_trendline.txt');
        $results = [];
        $globalAvg = 0;
        $feeds = 0;

        foreach ([$frequencyHistogramGoogleChartDataFile, $frequencySubscribersTrendlineGoogleChartDataFile] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
        //File::put($frequencyHistogramGoogleChartDataFile, "['Feed', 'Frequenz']," . PHP_EOL);

        $yesterday = new \DateTimeImmutable('yesterday');
        $dateRange = ['from' => $yesterday->sub(new \DateInterval('P1W')), 'to' => $yesterday];
        $sl = new StatisticsLegacy();

        Feed::options(['allowDiskUse' => true])->chunk(100, function ($channels) use (&$results, &$globalAvg, &$feeds,
            $sl, $dateRange, $frequencyHistogramGoogleChartDataFile, $frequencySubscribersTrendlineGoogleChartDataFile) {
            foreach($channels as $feed) {
                $shows = $feed->shows;
                $countShows = $feed->shows->count();

                if($countShows >= 5) {
                    $minDate = $time = time();
                    $maxDate = -1;

                    foreach($shows as $show) {
                        if ($show->lastUpdate < $minDate) {
                            if ($show->lastUpdate) {
                                $minDate = $show->lastUpdate;
                            }

                            if ($maxDate === -1) {
                                $maxDate = $show->lastUpdate;
                            }
                        }

                        if ($show->lastUpdate > $maxDate) {
                            $maxDate = $show->lastUpdate;
                        }
                    }

                    $avg = (($maxDate-$minDate)/$countShows); // in seconds

                    if ($avg > 120) {
                        $results[$feed->username][$feed->feed_id]['seconds'] = $avg;
                        $days = round($avg/86400, 2);
                        $subscribers = $sl->getSubscriberCount($feed->username, $dateRange, $feed->feed_id);
                        //$subscribers = rand(0, 14000);
                        $results[$feed->username][$feed->feed_id]['days'] = $days;
                        $results[$feed->username][$feed->feed_id]['subscribers'] = $subscribers;
                        $results[$feed->username][$feed->feed_id]['count'] = $countShows;
                        $results['_DAYS_'][] = $days;
                        $results['_SUBSCRIBERS_'][] = $subscribers;
                        file_put_contents($frequencyHistogramGoogleChartDataFile, "['$feeds', '{$days}'],", FILE_APPEND);
                        file_put_contents($frequencySubscribersTrendlineGoogleChartDataFile, "[$days, {$subscribers}],", FILE_APPEND);
                        $globalAvg += $avg;
                        $feeds++;
                    }
                }
            };
        });

        $resultCollection = new Collection($results);

        $this->line("Feeds: {$feeds}");
        $avg = $globalAvg/$feeds;
        $median = $this->calculateMedian($results['_DAYS_']);
        $this->line("Durchschnitt alle " . round($avg/86400, 2) . " Tage, Median alle " . round($median, 2) . " Tage.");

    }

    private function calculateMedian($arr)
    {
        sort($arr);
        $count = count($arr); //total numbers in array
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
        if($count % 2) { // odd number, middle is the median
            $median = $arr[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }
}
