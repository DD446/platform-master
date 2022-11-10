<?php
/**
 * User: fabio
 * Date: 28.02.22
 * Time: 14:01
 */

namespace App\Classes;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\Show;
use App\Models\UserUpload;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Str;

class StatisticsElastic
{
    /**
     * @param  string  $username
     * @param  array  $range
     * @param  array|null  $page
     * @param  string|null  $feedId
     * @param  string|null  $episodeId
     * @return array
     */
    public function getShows(string $username, array $range, ?array $page = ['size' => 5, 'number' => 1], ?string $feedId = null, ?string $episodeId = null): array
    {
        $collectionId = null;

        if (Str::startsWith($feedId, ['at-', 'pp-']) && Str::length(Str::after($feedId, 'at-')) <= 7) {
            $collectionId = $feedId;
            $feedId = AudiotakesContract::withTrashed()->where('identifier', '=', $collectionId)->select(['feed_id'])->firstOrFail()->value('feed_id');
        }

        if (!is_null($feedId)) {
            $feed = Feed::whereUsername($username)->whereFeedId($feedId)->first();
            $domain = $feed->subdomain;
        } else {
            $feeds = Feed::whereUsername($username)->get();
            $feed = $feeds[0];
            $domain = $feed->subdomain;

            if (!$collectionId) {
                $feedIds =  $feeds->map(function($item) { return $item->feed_id; })->toArray();
                // TODO : Remove but needs rework on all levels
                $feedId = $feedIds[0];
            }
        }

        if ($episodeId) {
            try {
                $show = get_show_by_aid($feed, Str::afterLast($episodeId, '-'));
            } catch (\Exception $e) {
                $show = [
                    'title' => $e->getMessage()
                ];
                Log::error("ERROR: User $username: Feed $feedId: Show by audiotakes id $episodeId not found.");
            }
        }

        $_items = collect();

/*        foreach ($feeds as $feed) {
            $shows = $feed->shows;
            $_items = $_items->merge($shows->reject(function ($value) {
                return !$value['show_media'] || !in_array($value['is_public'], [Show::PUBLISH_PAST, Show::PUBLISH_NOW]);
            }));
        }

        $_items = $_items->sortByDesc(
            [
                fn ($a, $b) => $b->lastUpdate <=> $a->lastUpdate
            ]
        )->take($page['size']);*/

        $legend = [];
        $series = [];
        $sources = [];

        // This is important to make sure data belongs to this user
        // feedId can be same on different accounts
        $sources['domain'] = $domain;

        if (!is_null($feedId)) {
            $sources['feed'] = $feedId;
        }

/*        if (!is_null($media)) {
            $sources['media'] = $media;
        }*/

        if (!is_null($collectionId)) {
            $sources['collection_id'] = $collectionId;
        }

        if (!is_null($episodeId)) {
            $sources['episode_id'] = $episodeId;
        }

        // 2 MB default file size to count as download
        $filesizeMinimum = 2097152;

        if ($show && isset($show['show_media']) && $show['show_media']) {
            $minSize = UserUpload::owner()->where('file_id', '=', $show['show_media'])->value('iab_min_size');

            if ($minSize) {
                $filesizeMinimum = $minSize;
            }
        }

        $shows = (new ElasticManager())->getShows($range, $sources, $filesizeMinimum);

        $xAxis = [];
        $downloads = [];
        $streams = [];
        $total = [];
        $rows = [];
        $period = CarbonPeriod::create($range['df'], $range['dt']);
        foreach ($period as $date) {
            $_date = $date->startOfDay()->getTimestampMs();
            $xAxis[$_date] = $_date;
/*            $downloads[$date->getTimestampMs()] = [
                'date' => $date->getTimestampMs(),
            ];*/
            $downloads[$_date] = 0;
            $streams[$_date] = 0;
            $total[$_date] = 0;
            $rows[$_date] = [
                'date' => $_date,
                'downloads' => 0,
                'streams' => 0,
                'total' => 0,
            ];
        }

        $legend[] = 'Downloads';

        foreach ($shows->aggregations()->get('downloads_over_time_per_media')->raw()['downloads']['buckets'] as $bucket) {
            $timestampMs = CarbonImmutable::createFromTimestampMs($bucket['key'])->startOfDay()->getTimestampMs();

            if (array_key_exists($timestampMs, $downloads)) {
                $downloads[$timestampMs] = $bucket['doc_count'];
                $total[$timestampMs] += $bucket['doc_count'];
                $rows[$timestampMs]['downloads'] = $bucket['doc_count'];
                $rows[$timestampMs]['total'] += $bucket['doc_count'];
            }
        }

        $series[] = [
            'name' => "Downloads",
            'type' => 'line',
            'smooth' => false,
            'showSymbol' => true,
            'data' => array_values($downloads),
        ];

        foreach ($shows->aggregations()->get('streams_over_time_per_media')->raw()['streams']['buckets'] as $bucket) {
            $timestampMs = CarbonImmutable::createFromTimestampMs($bucket['key'])->startOfDay()->getTimestampMs();

            if (array_key_exists($timestampMs, $streams)) {
                $streams[$timestampMs] = $bucket['valid_streams']['count'];
                $total[$timestampMs] += $bucket['valid_streams']['count'];

                $rows[$timestampMs]['streams'] = $bucket['valid_streams']['count'];
                $rows[$timestampMs]['total'] += $bucket['valid_streams']['count'];
            }
        }

        $legend[] = 'Streams';
        $series[] = [
            'name' => "Streams",
            'type' => 'line',
            'smooth' => false,
            'showSymbol' => true,
            'data' => array_values($streams),
        ];

        $legend[] = 'Total';
        $series[] = [
            'name' => "Total",
            'type' => 'line',
            'smooth' => false,
            'showSymbol' => true,
            'data' => array_values($total),
        ];

/*
        $countries = [];
        $countrySum = $shows->aggregations()->get('country_count')->buckets()->sum(function ($i) {
            return $i->docCount();
        });
        $countrySum += $shows->aggregations()->get('country_count')->raw()['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('country_count')->buckets() as $bucket) {
            $countries[] = [
                'name' => $bucket->key(),
                'value' => $bucket->docCount(),
                'percentage' => ($countrySum > 0 ? round($bucket->docCount()/$countrySum*100, 2) : 0) . '%'
            ];
        }   */

        $countries = [];
        $countrySum = 0;
        foreach ($shows->aggregations()->get('country_count')->raw()['countries']['buckets'] as $aBucket) {
            $countrySum += $aBucket['doc_count'];
        }
        $countrySum += $shows->aggregations()->get('country_count')->raw()['countries']['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('country_count')->raw()['countries']['buckets'] as $aBucket) {
            $countries[] = [
                'name' => $aBucket['key'],
                'value' => $aBucket['doc_count'],
                'percentage' => ($countrySum > 0 ? round($aBucket['doc_count']/$countrySum*100, 2) : 0) . '%'
            ];
        }

/*
        $os = [];
        $osSum = $shows->aggregations()->get('osfamily_count')->buckets()->sum(function ($i) {
            return $i->docCount();
        });
        $osSum += $shows->aggregations()->get('osfamily_count')->raw()['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('osfamily_count')->buckets() as $bucket) {
            $os[] = [
                'name' => $bucket->key(),
                'value' => $bucket->docCount(),
                'percentage' => ($osSum > 0 ? round($bucket->docCount()/$osSum*100, 2) : 0) . '%'
            ];
        }*/

        $os = [];
        $osSum = 0;
        foreach ($shows->aggregations()->get('osfamily_count')->raw()['operating_systems']['buckets'] as $aBucket) {
            $osSum += $aBucket['doc_count'];
        }
        $osSum += $shows->aggregations()->get('osfamily_count')->raw()['operating_systems']['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('osfamily_count')->raw()['operating_systems']['buckets'] as $aBucket) {
            $os[] = [
                'name' => $aBucket['key'],
                'value' => $aBucket['doc_count'],
                'percentage' => ($osSum > 0 ? round($aBucket['doc_count']/$osSum*100, 2) : 0) . '%'
            ];
        }

/*
        $clientTypes = [];
        $sum = $shows->aggregations()->get('client_type_count')->buckets()->sum(function ($i) {
            return $i->docCount();
        });
        $sum += $shows->aggregations()->get('client_type_count')->raw()['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('client_type_count')->buckets() as $bucket) {
            $clientTypes[] = [
                'name' => trans('audiotakes.client_type_' . $bucket->key()),
                'value' => $bucket->docCount(),
                'percentage' => ($sum > 0 ? round($bucket->docCount()/$sum*100, 2) : 0) . '%'
            ];
        }*/

        $clientTypes = [];
        $clientTypeSum = 0;
        foreach ($shows->aggregations()->get('client_type_count')->raw()['client_types']['buckets'] as $aBucket) {
            $clientTypeSum += $aBucket['doc_count'];
        }
        $clientTypeSum += $shows->aggregations()->get('client_type_count')->raw()['client_types']['sum_other_doc_count'];
        foreach ($shows->aggregations()->get('client_type_count')->raw()['client_types']['buckets'] as $aBucket) {
            $clientTypes[] = [
                'name' => $aBucket['key'],
                'value' => $aBucket['doc_count'],
                'percentage' => ($clientTypeSum > 0 ? round($aBucket['doc_count']/$clientTypeSum*100, 2) : 0) . '%'
            ];
        }

/*
        $apps = [];
        foreach ($shows->aggregations()->get('apps_count')->buckets() as $bucket) {
            if ($bucket->key() != 'mobile app') {
                continue;
            }
            $sum = $bucket->docCount();
            foreach ($bucket->raw()['clients']['buckets'] as $_bucket) {
                $apps[] = [
                    'name' => $_bucket['key'],
                    'value' => $_bucket['doc_count'],
                    'percentage' => ($sum > 0 ? round($_bucket['doc_count'] / $sum * 100, 2) : 0).'%'
                ];
            }
        }*/

        $apps = [];
        $appsSum = 0;
        foreach ($shows->aggregations()->get('apps_count')->raw()['apps']['buckets'] as $aBucket) {
            // TODO: add this to filter in request
            if ($aBucket['key'] != 'mobile app') {
                continue;
            }
            $appsSum += $aBucket['clients']['sum_other_doc_count'];
            foreach($aBucket['clients']['buckets'] as $_aBucket) {
                $appsSum += $_aBucket['doc_count'];
            }
        }
        foreach ($shows->aggregations()->get('apps_count')->raw()['apps']['buckets'] as $aBucket) {
            // TODO: add this to filter
            if ($aBucket['key'] != 'mobile app') {
                continue;
            }
            foreach ($aBucket['clients']['buckets'] as $_bucket) {
                $apps[] = [
                    'name' => $_bucket['key'],
                    'value' => $_bucket['doc_count'],
                    'percentage' => ($appsSum > 0 ? round($_bucket['doc_count'] / $appsSum * 100, 2) : 0).'%'
                ];
            }
        }

//        $uniqueListeners = $shows->aggregations()->get('unique_listeners')->raw()['value'];

/*        $listeners = 0;
        foreach ($shows->aggregations()->get('listeners_over_time')->buckets() as $bucket) {
            //$timestamp = $bucket->key();
            $listeners += $bucket->raw()['distinct_listeners']['value'];
        }*/

        return [
            'option' => [
                'legend' => $legend,
                'title' => [
                    'text' => $show['title'],
                    'subtext' => "Downloads & Streams",
                    'left' => 'center'
                ],
                'xAxis' => array_values($xAxis),
                'series' => $series,
            ],
            'table' => $rows,
            //'referer' => $referer
            'countries' => $countries,
            'os' => $os,
            'clientTypes' => $clientTypes,
            'apps' => $apps
            //'uniqueListeners' => ['now' => $uniqueListeners],
            //'listeners' => ['now' => $listeners]
        ];
    }
}
