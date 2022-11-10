<?php

namespace App\Http\Controllers\API;

use App\Classes\Statistics;
use App\Http\Requests\StatsSubscribersRequest;
use App\Models\ExternalSubscriber;
use App\Models\Feed;
use App\Models\Subscription;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class StatsSubscriberController extends Controller
{
    /**
     * Subscribers
     *
     * Fetch subscriber data of your podcasts.
     *
     * @group Statistics
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     * @authenticated
     * @hideFromAPIDocumentation
     */
    public function index(StatsSubscribersRequest $request)
    {
        $validated = $request->validated();
        $feedId = $validated['feed_id'] ?? null;
        $type = 'day';
        $statistics = new \App\Classes\Statistics();
        $feeds = Feed::owner()
            ->when($feedId, function($query) use ($feedId) {
                return $query->whereIn('feed_id', [$feedId]);
            })
            ->get(['feed_id', 'rss.title'])
            ->mapWithKeys(function($item, $key) { return [$item['feed_id'] => $item['rss']['title']]; });
        $keys = ['date', 'hits', 'feed_id', 'user_agent_type'];
        $subscriptions = Subscription::whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
            ->where('type', $type)
            ->when($feedId, function($query) use ($feedId) {
                return $query->whereIn('feed_id', [$feedId]);
            })
            ->whereIn('date', $statistics->getDatesByRange($validated['df'], $validated['dt'], $type))
            ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
            ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
            ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
            ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
            ->get($keys)
            ->groupBy('feed_id')
            ->all();
        $results = ['all' => ['title' => 'Gesamt', 'total' => [], 'totals' => 0], 'feeds' => []];

        foreach($feeds as $_feedId => $title) {
            $results['feeds'][$_feedId] = ['title' => $title, 'total' => [], 'totals' => 0];
        }

        $period = CarbonPeriod::create($validated['df'], $validated['dt']);

        foreach ($period as $date) {
            $startOfDay = $date->setTimezone(config('app.timezone'))->startOfDay()->getTimestampMs();
            $results['all']['total'][$startOfDay] = 0;

            foreach($results['feeds'] as $_feedId => $a) {
                $results['feeds'][$_feedId]['total'][$startOfDay] = 0;
            }
        }

        foreach ($subscriptions as $_feedId => $data) {
            // Only include data for existing feeds
            if (!$feeds->has($_feedId)) continue;

            foreach($data as $row) {
                $date = Carbon::createFromFormat(Statistics::getDateFormatter($type), $row['date'])->startOfDay()->getTimestampMs();
                if (!array_key_exists($date, $results['all']['total'])) {
                    $results['all']['total'][$date] = $row['hits'];
                } else {
                    $results['all']['total'][$date] += $row['hits'];
                }

                $results['all']['totals'] += $row['hits'];

                if (!array_key_exists($date, $results['feeds'][$_feedId]['total'])) {
                    $results['feeds'][$_feedId]['total'][$date] = $row['hits'];
                } else {
                    $results['feeds'][$_feedId]['total'][$date] += $row['hits'];
                }

                $results['feeds'][$_feedId]['totals'] += $row['hits'];
            }
        }

        return response()->json($results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     *
     */
    public function extern()
    {
        $yesterday = CarbonImmutable::yesterday()->format('Y-m-d');
        $subscribers = ExternalSubscriber::groupBy('user_agent')->where('date', '=', $yesterday)->get(['user_agent', 'subscribers']);

        return response()->json($subscribers);
    }

    public function country(string $type = 'combined')
    {
        Subscription::whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
            ->where('type', $type)
            ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
            ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
            ->whereRaw(['geo' => ['$exists' => true]], [], 'and')
            ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
            ->sum('hits');
    }
}
