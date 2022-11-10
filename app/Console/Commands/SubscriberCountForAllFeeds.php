<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\StatisticsLegacy;
use App\Models\Feed;
use App\Models\User;

class SubscriberCountForAllFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:subscriber-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates, retrieves and caches the number of subscribers for all feeds';

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
        $yesterday = new \DateTimeImmutable('yesterday');
        $dateRange = ['from' => $yesterday->sub(new \DateInterval('P1W')), 'to' => $yesterday];
        $sl = new StatisticsLegacy();
        $i = 1;
        $feeds = Feed::select(['username', 'feed_id'])->get();
        $feeds->each(function($feed) use ($sl, $dateRange, &$i) {
            $count = $sl->getSubscriberCount($feed->username, $dateRange, $feed->feed_id);
            $this->line("{$i}. Subscriber count for feed '{$feed->feed_id}' ({$feed->username}): {$count}");
            $feed->subscriber_count = $count;
            $i++;
        });

        $grouped = $feeds->groupBy(function ($item) {
            if ($item['subscriber_count'] < 1) {
                return 'zero';
            } elseif ($item['subscriber_count'] < 1000) {
                return '1-999';
            } elseif ($item['subscriber_count'] >= 1000 && $item['subscriber_count'] < 5000) {
                return '1000-4999';
            } elseif ($item['subscriber_count'] >= 5000 && $item['subscriber_count'] < 10000) {
                return '5000-9999';
            } elseif ($item['subscriber_count'] >= 10000 && $item['subscriber_count'] < 50000) {
                return '10000-49999';
            } else {
                return '50000+';
            }
        });

        $subscriberCount = storage_path() . 'subscriber_count.txt';

        foreach($grouped as $key => $group) {
            $line = "Group `{$key}` count: " . $group->count();
            file_put_contents($subscriberCount, $line, FILE_APPEND);
            $this->line($line);
        }
    }
}
