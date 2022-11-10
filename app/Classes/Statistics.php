<?php
/**
 * User: fabio
 * Date: 13.07.20
 * Time: 18:22
 */

namespace App\Classes;


use App\Models\Feed;
use App\Models\Request;
use App\Models\Show;
use App\Models\StatsExport;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\Download;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\CopyFormat;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class Statistics
{
    const ALL_FEEDS = '__FEEDS__';

    const ALL_SHOWS = '__SHOWS__';

    const LOGFILE_DATE_YEAR                 = 'Y';
    const LOGFILE_DATE_QUARTER              = 'Q-Y';
    const LOGFILE_DATE_MONTH                = 'M-Y';
    const LOGFILE_DATE_WEEK                 = 'o-W';
    const LOGFILE_DATE_DAY                  = 'd-M-Y';
    const LOGFILE_DATE_HOUR                 = 'd-M-Y h';

    /**
     * @param $username
     * @param $range
     * @param  string  $type
     * @param  string|null  $source
     * @return array
     * @throws \Exception
     */
    public function listeners($username, $range, string $type = 'day', ?string $source = null): array
    {
        $aRange = $this->getRange($range, $source);

        if ($aRange['range'] == 'alltime') {
            return $this->listenersAlltime($username, $source);
        }
        list('cacheId' => $cacheId, 'dates' => $dates, 'prevDates' => $prevDates) = $aRange;

        return Cache::tags(['listeners_' . $cacheId])->remember('STATS_LISTENERS_' . $username . '_' . $cacheId, 3600, function () use ($dates, $prevDates, $type, $source) {
            $requests = $this->getHits('podcast', $dates, $type, $source);

            // This is only relevant if results are not limited to specific podcast
            if (is_null($source)) {
                $downloads = $this->getHits('direct', $dates, $type);
            } else {
                $downloads = 0;
            }
            // $downloads is from direct requests - bad nameing
            $hits = $requests + $downloads;
            $percentChange = null;
            $requests = $this->getHits('podcast', $prevDates, $type, $source);

            // This is only relevant if results are not limited to specific podcast
            if (is_null($source)) {
                $downloads = $this->getHits('direct', $prevDates, $type);
            } else {
                $downloads = 0;
            }
            $prevHits = $requests + $downloads;

            if ($prevHits > 0) {
                $percentChange = round( ($hits - $prevHits) / $prevHits * 100 );
            }

            return ['now' => $hits, 'prev' => $prevHits, 'change' => $percentChange];
        });
    }

    /**
     * @param  string  $username
     * @param  array  $page
     * @param  int  $start  Unix timestamp
     * @param  int  $end  Unix timestamp
     * @param  string|null  $source
     * @return array
     * @throws \Exception
     */
    public function lastshows(string $username, array $page, int $start, int $end, ?string $source = null): array
    {
        $df = CarbonImmutable::createFromTimestamp($start)->startOfDay()->unix()*1000;
        $dt = CarbonImmutable::createFromTimestamp($end)->endOfDay()->unix()*1000;
        $dateFrom = new \MongoDB\BSON\UTCDateTime($df);
        $dateTo = new \MongoDB\BSON\UTCDateTime($dt);

        if (!is_null($source)) {
            $feeds = Feed::whereUsername($username)->whereFeedId($source)->get();
        } else {
            $feeds = Feed::whereUsername($username)->get();
        }
        $_items = collect();

        foreach ($feeds as $feed) {
            $shows = $feed->shows;
            $_items = $_items->merge($shows->reject(function ($value) {
                return !$value['show_media'] || !in_array((int)$value['is_public'], [Show::PUBLISH_PAST, Show::PUBLISH_NOW]);
            }));
        }

        $_items = $_items->sortByDesc(
            [
                fn ($a, $b) => $b->lastUpdate <=> $a->lastUpdate
            ]
        )->take($page['size']);

        $r = new \App\Models\Request(['username' => $username]);

        $items = [];
        foreach ($_items as $item) {
            $file = get_file($item['feed']->username, $item['show_media']);
            if (!$file) {
                continue;
            }
            $cover = null;
            $media = $file['name'];
            $hits = $r->where('feed_id', $item['feed']->feed_id)
                ->where('media', $media)
                ->whereType('day')
                ->where('created', '>=', $dateFrom)
                ->where('created', '<=', $dateTo)
                ->whereNull('geo')
                ->whereNull('operating_system')
                ->whereNull('user_agent')
                ->whereNotNull('user_agent_type')
                //->whereNotIn('user_agent_type', ['bots', 'unknown'])
                ->whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
                ->sum('hits');

            if (isset($item['itunes']['logo'])) {
                try {
                    $_logo = get_file($username, $item['itunes']['logo']);
                    $cover = get_image_uri($item['feed']->feed_id, $_logo['name'], $item['feed']->domain);
                } catch (\Exception $e) {
                }
            } else if (isset($item['feed']->logo['itunes']) && is_numeric($item['feed']->logo['itunes'])) {
                try {
                    $_logo = get_file($username, $item['feed']->logo['itunes']);
                    $cover = get_image_uri($item['feed']->feed_id, $_logo['name'], $item['feed']->domain);
                } catch (\Exception $e) {
                }
            }

            if (!$cover) {
                $cover = '/images1/no-user.png';
            }

            $item = [
                'title' => $item['title'],
                'file' => $file['name'],
                'podcast' => $item['feed']->rss['title'],
                'published' => CarbonImmutable::createFromTimestamp($item['lastUpdate'])->isoFormat('DD.MM.YYYY'), // TODO: I18N
                'hits' => $hits,
                'cover' => $cover
            ];
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param  string  $username
     * @param  array  $page
     * @param  string|null  $source
     * @return array
     */
    public function charts(string $username, array $page, ?string $source = null): array
    {
        $r = new \App\Models\Request(['username' => $username]);
        $_items = $r->raw(function ($collection) use ($page, $source) {
            $matching = [
                'type' => 'combined',
                'geo' => [ '$exists' => false ],
                'operating_system' => [ '$exists' => false ],
                'user_agent' => [ '$exists' => false ],
                'user_agent_type' => [ '$exists' => true, '$in' => ["apps", "browsers", "desktop"] ]
            ];

            if (!is_null($source)) {
                $matching['feed_id'] = $source;
            }

            $params = [
                [ '$match' => $matching ],
                [ '$group' => [ '_id' => [ 'media' => '$media', 'feed_id' => '$feed_id' ], 'total' => [ '$sum' => '$hits' ] ] ],
                [ '$sort' => [ 'total' => -1 ] ],
                [ '$limit' => (int)$page['size'] ],
            ];
            return $collection->aggregate($params);
        });

        if (!is_null($source)) {
            $feeds = Feed::whereUsername($username)->whereFeedId($source)->get();
        } else {
            $feeds = Feed::whereUsername($username)->get();
        }

        $aFeedIdTitle = $feeds->mapWithKeys(function ($item, $key) {
            return [$item->feed_id => $item->rss['title']];
        })->toArray();

        $items = [];
        $place = 1;

        foreach ($_items as $item) {
            $feedTitle = null;
            $cover = null;

            if (array_key_exists($item->_id->feed_id, $aFeedIdTitle)) {
                $feedTitle = $aFeedIdTitle[$item->_id->feed_id];
            }

            try {
                $file = get_file($username, get_file_id_by_filename($username, $item->_id->media));
            } catch (\Exception $e) {
                continue;
            }
            if (!$file) {
                continue;
            }

            $_feed = $feeds->where('feed_id', $item->_id->feed_id)->first();

            if (!$_feed) {
                continue;
            }

            $shows = collect($_feed->entries);
            $show = $shows->where('show_media', $file['id'])->first();
            if (!$show || !isset($show['title'])) {
                // This should not happen but just in case
                // Show could be deleted, etc.
                continue;
            }

            if (isset($item['itunes']['logo'])) {
                try {
                    $_logo = get_file($username, $item['itunes']['logo']);
                    $cover = get_image_uri($_feed->feed_id, $_logo['name'], $_feed->domain);
                } catch (\Exception $e) {
                }
            } else if (isset($_feed->logo['itunes']) && is_numeric($_feed->logo['itunes'])) {
                try {
                    $_logo = get_file($username, $_feed->logo['itunes']);
                    $cover = get_image_uri($_feed->feed_id, $_logo['name'], $_feed->domain);
                } catch (\Exception $e) {
                }
            }

            if (!$cover) {
                $cover = '/images1/no-user.png';
            }

            $items[] = [
                'place' => $place . '.',
                'title' => $show['title'],
                'file' => $file['name'],
                'podcast' => $feedTitle,
                'published' => CarbonImmutable::createFromTimestamp($show['lastUpdate'])->formatLocalized('%d.%m.%Y'), // TODO: I18N
                'hits' => $item->total,
                'cover' => $cover
            ];
            $place++;
        }

        return $items;
    }

    /**
     * @param  string  $username
     * @param  array  $page
     * @param  int  $start
     * @param  int  $end
     * @param  string|null  $source
     * @return array
     */
    public function topshows(string $username, array $page, int $start, int $end, ?string $source = null): array
    {
        $df = CarbonImmutable::createFromTimestamp($start)->startOfDay()->unix()*1000;
        $dt = CarbonImmutable::createFromTimestamp($end)->endOfDay()->unix()*1000;
        $dateFrom = new \MongoDB\BSON\UTCDateTime($df);
        $dateTo = new \MongoDB\BSON\UTCDateTime($dt);

        $r = new \App\Models\Request(['username' => $username]);
        $_items = $r->raw(function ($collection) use ($dateFrom, $dateTo, $page, $source) {
            $match = [
                'type' => 'day',
                'geo' => [ '$exists' => false ],
                'operating_system' => [ '$exists' => false ],
                'user_agent' => [ '$exists' => false ],
                'user_agent_type' => [ '$exists' => true, '$in' => ["apps", "browsers", "desktop"]  ],
                "created" => [ '$gte' => $dateFrom, '$lte' => $dateTo ]
            ];

            if (!is_null($source)) {
                $match['feed_id'] = $source;
            }

            return $collection->aggregate([
                [ '$match' => $match],
                [ '$group' => [ '_id' => [ 'media' => '$media', 'feed_id' => '$feed_id' ], 'total' => [ '$sum' => '$hits' ] ] ],
                [ '$sort' => [ 'total' => -1 ] ],
                [ '$limit' => (int)$page['size'] ],
            ]);
        });

        $feeds = Feed::whereUsername($username)->get();
        $aFeedIdTitle = $feeds->mapWithKeys(function ($item, $key) {
            return [$item->feed_id => $item->rss['title']];
        })->toArray();
        $items = [];
        foreach ($_items as $item) {
            $feedTitle = null;
            $cover = null;

            if (array_key_exists($item->_id->feed_id, $aFeedIdTitle)) {
                $feedTitle = $aFeedIdTitle[$item->_id->feed_id];
            }

            try {
                $file = get_file($username, get_file_id_by_filename($username, $item->_id->media));
            } catch (\Exception $e) {
                continue;
            }
            if (!$file) {
                continue;
            }
            $feed = $feeds->where('feed_id', $item->_id->feed_id)->first();

            if (!$feed) {
                continue;
            }
            $shows = collect($feed->entries);
            $show = $shows->where('show_media', $file['id'])->first();
            if (!$show || !isset($show['title'])) {
                // This should not happen but just in case
                // Show could be deleted, etc.
                continue;
            }

            if (isset($item['itunes']['logo'])) {
                try {
                    $_logo = get_file($username, $show['itunes']['logo']);
                    $cover = get_image_uri($feed->feed_id, $_logo['name'], $feed->domain);
                } catch (\Exception $e) {
                }
            } else if (isset($feed->logo['itunes']) && is_numeric($feed->logo['itunes'])) {
                try {
                    $_logo = get_file($username, $feed->logo['itunes']);
                    $cover = get_image_uri($feed->feed_id, $_logo['name'], $feed->domain);
                } catch (\Exception $e) {
                }
            }

            if (!$cover) {
                $cover = '/images1/no-user.png';
            }

            $items[] = [
                'title' => $show['title'],
                //'logo' => get_intern_uri($show[]),
                'file' => $file['name'],
                'podcast' => $feedTitle,
                'published' => CarbonImmutable::createFromTimestamp($show['lastUpdate'])->formatLocalized('%d.%m.%Y'), // TODO: I18N
                'hits' => $item->total,
                'cover' => $cover,
            ];
        }

        return $items;
    }

    /**
     * @param  string  $username
     * @param $range
     * @param  string|null  $type
     * @param  string|null  $source
     * @return array
     * @throws \Exception
     */
    public function subscribers(string $username, $range, ?string $type = 'day', ?string $source = null): array
    {
        $aRange = $this->getRange($range, $source);

        if ($aRange['range'] == 'alltime') {
            throw new \Exception('Unknown or missing range.');
        }
        list('cacheId' => $cacheId, 'dates' => $dates, 'prevDates' => $prevDates) = $aRange;

        if (!$dates) {
            throw new \Exception('Not implemented, yet.');
        }

        return Cache::tags(['subscribers_' . $cacheId])->remember(
            'STATS_SUBSCRIBERS_' . $username . '_' . $cacheId,
            3600,
            function () use ($dates, $prevDates, $type, $source) {
                $hits = (int) round( $this->getSubscribersByDay($dates, $type, $source) / count($dates) );
                $prevHits = (int) round( $this->getSubscribersByDay($prevDates, $type, $source) / count($dates));
                $percentChange = '-';

                if ($prevHits > 0) {
                    $percentChange = round( ($hits - $prevHits) / $prevHits * 100 );
                }

                return ['now' => $hits, 'prev' => $prevHits, 'change' => $percentChange];
            });
    }

    /**
     * @param  string  $origin
     * @param  array  $dates
     * @param  string|null  $type
     * @param  string|null  $source
     * @return mixed
     * @throws \Exception
     */
    private function getHits(string $origin, array $dates, ?string $type = 'day', ?string $source = null)
    {
        switch (Str::lower($origin)) {
            case 'podcast':
                $m = new \App\Models\Request();
                break;
            case 'direct':
                $m = new Download();
                break;
            default:
                throw new \Exception("Unknown source: `{$origin}`.");
        }

        return $m->whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
            ->where('type', $type)
            ->whereIn('date', $dates)
            ->when($source, function($query) use ($source) {
                return $query->where('feed_id', '=', $source);
            })
            ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
            ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
            ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
            ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
            ->sum('hits');
    }

    /**
     * @param  array  $dates
     * @param  string|null  $type
     * @param  string|null  $source
     * @return mixed
     */
    private function getSubscribersByDay(array $dates, ?string $type = 'day', ?string $source = null)
    {
        return \App\Models\Subscription::whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
            ->where('type', $type)
            ->whereIn('date', $dates)
            ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
            ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
            ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
            ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
            ->when($source, function($query) use ($source) {
                return $query->where('feed_id', '=', $source);
            })
            ->sum('hits');
    }

    /**
     * @param  string  $username
     * @return array
     */
    private function listenersAlltime(string $username, ?string $source = null): array
    {
        $cacheId = 'alltime_' . Str::lower($source);

        return Cache::tags(['listeners_' . $cacheId])->remember('STATS_LISTENERS_' . $username . '_' . $cacheId, 86400, function () use ($source) {
            $requests = Request::whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
                ->where('type', 'combined')
                ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
                ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
                ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
                ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
                ->when($source, function($query) use ($source) {
                    return $query->where('feed_id', '=', $source);
                })
                ->sum('hits');

            if (is_null($source)) {
                $downloads = Download::whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
                    ->where('type', 'combined')
                    ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
                    ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
                    ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
                    ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
                    ->sum('hits');
            } else {
                $downloads = 0;
            }
            $hits = $requests + $downloads;

            return ['now' => $hits];
        });
    }

    /**
     * @param $range
     * @param  string|null  $source
     * @return array
     * @throws \Exception
     */
    private function getRange($range, ?string $source = null): array
    {
        $dates = null;
        $prevDates = null;
        $cacheId = null;

        if (is_array($range)) {
            if (!array_key_exists('df', $range)) {
                throw new \InvalidArgumentException('Missing key "df".');
            }
            if (!array_key_exists('dt', $range)) {
                throw new \InvalidArgumentException('Missing key "dt');
            }
        } else {
            switch (Str::lower($range)) {
                case 'yesterday':
                    $yesterday = new \DateTimeImmutable('yesterday');
                    $dates = [$yesterday->format('d-M-Y')];
                    $prevDates = [CarbonImmutable::now()->subDays(8)->format('d-M-Y')];
                    $cacheId = $range;
                    break;
                case 'last7days':
                case 'last30days':
                case 'lastquarter':
                case 'quarter':
                case 'lastyear':
                case 'year':
                    if ($range === 'last7days') {
                        $firstPeriod = '-8 days';
                        $secondPeriod = '-15 days';
                        $recur = 7;
                    } elseif ($range === 'last30days') {
                        $firstPeriod = '-31 days';
                        $secondPeriod = '-61 days';
                        $recur = 30;
                    } elseif ($range === 'lastquarter') {
                        $now = new \DateTimeImmutable();
                        $offset = (($now->format('n') - 1) % 3)+3;
                        $firstPeriod = "first day of -{$offset} month midnight";
                        $secondOffset = $offset+3;
                        $secondPeriod = "first day of -{$secondOffset} month midnight -1 second";
                        //modify a copy of it to the first day of the current quarter
                        $firstOfQuarter = now()->firstOfQuarter();
                        //calculate the difference in days and add 1 to correct the index
                        $recur = 90;
                    } elseif ($range === 'quarter') {
                        $now = new \DateTimeImmutable();
                        $offset = ($now->format('n') - 1) % 3;
                        $firstPeriod = "first day of -{$offset} month midnight";
                        $secondOffset = $offset+3;
                        $secondPeriod = "first day of -{$secondOffset} month midnight -1 second";

                        //modify a copy of it to the first day of the current quarter
                        $firstOfQuarter = now()->firstOfQuarter();
                        //calculate the difference in days and add 1 to correct the index
                        $recur = now()->diffInDays($firstOfQuarter) + 1;
                    } elseif ($range === 'lastyear') {
                        $firstPeriod = "first day of last year midnight";
                        $secondPeriod = "first day of -2 years midnight";
                        $recur = 365;
                    } elseif ($range === 'year') {
                        $firstPeriod = "first day of this year midnight";
                        $secondPeriod = "first day of last year midnight";
                        $recur = now()->diffInDays(now()->firstOfYear()) + 1;
                    }
                    $period = new \DatePeriod(new \DateTime($firstPeriod), new \DateInterval('P1D'), $recur);
                    $dates = [];

                    foreach ($period as $date) {
                        $dates[] = $date->format('d-M-Y');
                    }

                    $period = new \DatePeriod(new \DateTime($secondPeriod), new \DateInterval('P1D'), $recur);
                    $prevDates = [];

                    foreach ($period as $date) {
                        $prevDates[] = $date->format('d-M-Y');
                    }

                    $cacheId = $range;
                    break;
                case 'alltime':
                    break;
                default:
                    throw new \Exception('Unknown or missing range.');
            }
        }

        return [
            'range' => $range,
            'dates' => $dates,
            'prevDates' => $prevDates,
            'cacheId' => $cacheId . '_' . Str::lower($source),
        ];
    }

    /**
     * @param  StatsExport  $export
     * @return bool
     * @throws \League\Csv\CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    public function getExport(StatsExport &$export): bool
    {
        $filename = 'podcaster_statistics_' . $export->user->username . '_' . time() . '_';
        $range = null;
        $limit = null;
        $offset = null;

        if ($export->start && $export->end) {
            $type = 'day';
            $range = [
                'start' => $export->start,
                'end' => $export->end,
            ];
            $filename .= date('Ymd', strtotime($range['start'])) . '-' . date('Ymd', strtotime($range['end'])) . '_';
        } else {
            $type = 'combined';
            $filename .= trans('stats.filename_all_time') . '_';
        }

        if ($export->limit) {
            $limit = $export->limit;
            $filename .= $export->limit . '-results';
        } else {
            $filename .= 'allresults_';
        }

        if ($export->offset) {
            $offset = $export->offset;
            $filename .= '-starting-at-' . $export->offset . '_';
        }

        $feedId = null;

        if ($export->feed_id && $export->feed_id != Statistics::ALL_FEEDS) {
            $feedId = $export->feed_id;
            $filename .= 'feed-' . $feedId . '_';
        } else {
            $filename .= trans('stats.filename_all_feeds') . '_';
        }

        if ($export->show_id && $export->show_id != Statistics::ALL_SHOWS) {
            // TODO
            $guid = $export->show_id;
            $filename .= 'show-' . $guid . '_';
        } else {
            $filename .= trans('stats.filename_all_shows') . '_';
        }

        $sortBy = $export->sort_by;
        $filename .= Str::lower($sortBy) . '-';

        $sortOrder = $export->sort_order;
        $filename .= Str::lower($sortOrder) . '_';

        if ($export->restrict) {
            if ($export->restrict_limit) {
                $filename .= $export->restrict_limit . '-';
            }
            if ($export->restrict == 'newest') {
            }
            $filename .= $export->restrict . '_';
        }

        $data = $this->getListenerData($export->user->username, $type, $feedId, $sortBy, $sortOrder, $range, $limit, $offset);
        $fields = [
            'show_title' => trans('stats.export_title_show_title'),
            'show_published' => trans('stats.export_title_show_published'),
            'show_filename' => trans('stats.export_title_show_filename'),
            'feed_title' => trans('stats.export_title_feed_title'),
            'feed_id' => trans('stats.export_title_feed_id'),
            'hits' => trans('stats.export_title_hits'),
        ];

        $filepath = storage_path('app/public/statistics/export');
        File::ensureDirectoryExists($filepath);
        $filename = rtrim($filename, '_');
        $csvExporter = new \Laracsv\Export();

        if ($export->format === 'csv') {
            $file = $filepath . DIRECTORY_SEPARATOR . $filename . '.' . $export->format;
            $filehandler = fopen($file, 'w');
            fwrite($filehandler, $csvExporter->build($data, $fields)->getWriter()->toString());
            fclose($filehandler);
        }

        if (!File::exists($file)) {
            throw new \Exception(trans('stats.error_creating_export_failed'));
        }

        $export->filename = $filename;

        return $export->update();
    }

    /**
     * @param  string  $username
     * @param  string  $type
     * @param  string|null  $feedId
     * @param  string  $sortBy
     * @param  string  $sortOrder
     * @param  array|null  $range
     * @param  int|null  $limit
     * @param  int|null  $offset
     * @param  string|null  $restrict
     * @param  int  $restrictLimit
     * @return Collection
     */
    protected function getListenerData(string $username, string $type = 'day', ?string $feedId = null, $sortBy = 'hits',
        $sortOrder = 'desc', ?array $range = null, ?int $limit = null, ?int $offset = null, ?string $restrict = null,
        int $restrictLimit = 10): Collection
    {
        if ($range) {
            $start = $range['start'];

            if (Str::contains($start, [':', '-'])) {
                $start = strtotime($start);
            }

            $end = $range['end'];

            if (Str::contains($end, [':', '-'])) {
                $end = strtotime($end);
            }
            $df = CarbonImmutable::createFromTimestamp($start)->startOfDay()->unix() * 1000;
            $dt = CarbonImmutable::createFromTimestamp($end)->endOfDay()->unix() * 1000;
            $dateFrom = new \MongoDB\BSON\UTCDateTime($df);
            $dateTo = new \MongoDB\BSON\UTCDateTime($dt);
        }

        $match = [
            'type' => $type,
            'geo' => [ '$exists' => false ],
            'operating_system' => [ '$exists' => false ],
            'user_agent' => [ '$exists' => false ],
            'user_agent_type' => [ '$exists' => true, '$in' => ["apps", "browsers", "desktop"]  ],
        ];

        if ($feedId) {
            $match['feed_id'] = $feedId;
        }

        if ($range) {
            $match['created'] = [ '$gte' => $dateFrom, '$lte' => $dateTo ];
        }

        if (Str::lower($sortOrder) == 'asc') {
            $sortOrder = 1;
        } else {
            $sortOrder = -1;
        }

        // There is no `date` field for type combined
        if (Str::lower($sortBy) == 'date' && $type != 'combined') {
            $sortBy = 'date';
        } else {
            $sortBy = 'total';
        }

        $group = [
            '_id' => [
                'media' => '$media',
                'feed_id' => '$feed_id'
            ],
            'total' => [
                '$sum' => '$hits'
            ]
        ];

        $aggregate = [
            [ '$match' => $match ],
            [ '$group' => $group ],
            [ '$sort' =>
                [
                    $sortBy => $sortOrder
                ]
            ],
        ];

        if ($limit) {
            $aggregate[] = [ '$limit' => (int)$limit ];
        }

        if ($offset) {
            $aggregate[] = [ '$skip' => (int)$offset ];
        }

        $r = new \App\Models\Request(['username' => $username]);
        $_items = $r->raw(function ($collection) use ($aggregate) {
            return $collection->aggregate($aggregate);
        });

        if ($restrict) {
            if ($restrict == 'newest') {
                $_items = $_items->sortByDesc(
                    [
                        fn ($a, $b) => $b->lastUpdate <=> $a->lastUpdate
                    ]
                )->take($restrictLimit);
            }
        }

        $feeds = Feed::whereUsername($username)->get();

        $aFeedIdTitle = $feeds->mapWithKeys(function ($item, $key) {
            return [$item->feed_id => $item->rss['title']];
        })->toArray();

        $items = new Collection();

        foreach ($_items as $item) {
            Log::debug("Processing feed: " . $item->_id->feed_id);
            $feedTitle = null;
            if (array_key_exists($item->_id->feed_id, $aFeedIdTitle)) {
                $feedTitle = $aFeedIdTitle[$item->_id->feed_id];
            }

            try {
                $file = get_file($username, get_file_id_by_filename($username, $item->_id->media));
            } catch (\Exception $e) {
                continue;
            }
            if (!$file) {
                continue;
            }
            $feed = $feeds->where('feed_id', $item->_id->feed_id)->first();

            if (!$feed) {
                continue;
            }
            $shows = collect($feed->entries);
            $show = $shows->where('show_media', $file['id'])->first();

            if (!$show || !isset($show['title'])) {
                // This should not happen but just in case
                // Show could be deleted, etc.
                continue;
            }

            $items[] = [
                'show_title' => $show['title'],
                'show_published' => CarbonImmutable::createFromTimestamp($show['lastUpdate'])->isoFormat('DD.MM.YYYY'), // TODO: I18N
                'show_filename' => $file['name'],
                'feed_title' => $feedTitle,
                'feed_id' => $item->_id->feed_id,
                'hits' => $item->total,
            ];
        }

        return $items;
    }

    public function test()
    {
        // db.requests_hoerspiele.aggregate([{$match: { "created" : { $gte: ISODate("2021-12-10T00:00:00.000Z") }, type: 'day', 'user_agent_type': { $in: ['apps','browsers','desktop'] }, 'user_agent': { $eq: null }, 'geo':  { $eq: null }, 'operating_system': { $eq: null } } }, { $group : { _id : { date  :"$date", media: "$media"  }, created : { $first: '$created' }, cnt : { $sum : "$hits" } } }, { $sort: { created: 1 } } ] );
        $r = new \App\Models\Request(['username' => 'hoerspiele']);
        $r->where('type','=','day')->where('created', '>=', now()->subDays(2))->whereNull('geo')->whereNull('operating_system')->whereNull('user_agent')->whereNotNull('user_agent_type')->whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])->where('type','=','day')->whereRaw(function ($collection) {
            return $collection->aggregate([ [ '$group' => [ '_id' => [ 'date' => '$date', 'media' => '$media', 'feed_id' => '$feed_id' ] ] ], [ '$sort' => [ 'created' => 1 ] ] ]);
        })->get()->take(5);

        $r->where('type','=','day')->where('created', '>=', now()->subDays(2))->whereNull('geo')->whereNull('operating_system')->whereNull('user_agent')->whereNotNull('user_agent_type')->whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])->where('type','=','day')->whereRaw(function ($collection) {
            return $collection->aggregate([ [ '$group' => [ '_id' => [ 'date' => '$date' ] ], 'created' => [ '$first' => '$created' ], 'cnt' => [ '$sum' => '$hits' ] ], [ '$sort' => [ 'created' => -1 ] ] ]);
        })->get()->take(5);

        $r->raw(function ($collection) {
            return $collection->aggregate(
                [
                    [
                        '$match' => [
                            'created' => [
                                '$exists' => true,
                                '$gte' => now()->subDays(2),
                            ],
                            'type' => 'day',
                            'user_agent_type' => [
                                '$in' => ['apps','browsers','desktop']
                            ],
                            'user_agent' => [
                                '$eq' => null
                            ],
                            'geo' => [
                                '$eq' => null
                            ],
                            'operating_system' => [
                                '$eq' => null
                            ]
                        ]
                    ]
                ],
                [ [ '$group' => [ '_id' => [ 'date' => '$date' ] ], 'created' => [ '$first' => '$created' ], 'cnt' => [ '$sum' => '$hits' ] ], [ '$sort' => [ 'created' => -1 ] ] ]);
        });
    }

    /**
     * @param  string  $file
     * @return false|int
     */
    public function getFileSizeOfFirstMinute(string $file)
    {
        $duration = 60;
        // Get extension from original file
        $extension = File::extension($file);

        if (!in_array($extension, ['mp3', 'wav', 'm4a', 'aac', 'ogg', 'mp2', 'flac', 'mp4', 'weba', 'webm', 'ogv', 'mpeg', 'mov'])) {
            return false;
        }

        // Construct temp file name
        $output = 'output.' . $extension;
        // Open file with ffmpeg
        try {
            $disk = Storage::disk('mediafiles');
            $path = $disk->path('');
            $_file = Str::after($file, $path);
            $media = FFMpeg::fromFilesystem($disk)
                ->open($_file);// Get duration of file
            $durationInSeconds = $media->getDurationInSeconds();// If duration is less than 60 seconds
            // the threshold is the complete file size
            if ($durationInSeconds < $duration) {
                return File::size($file);
            }

            // ffmpeg -t 60 -i inputfile.mp3 -acodec copy outputfile.mp3
            $start = \FFMpeg\Coordinate\TimeCode::fromSeconds(0);
            $end = \FFMpeg\Coordinate\TimeCode::fromSeconds($duration);
            if ($media->isVideo() && !in_array($extension, ['mp3', 'wav', 'm4a', 'ogg', 'flac', 'weba', 'aac', 'mp2'])) {
                $clipFilter = new \FFMpeg\Filters\Video\ClipFilter($start, $end);
            } else {
                $clipFilter = new \FFMpeg\Filters\Audio\AudioClipFilter($start, $end);
            }
            $res = $media
                ->addFilter($clipFilter)
                ->export()
                ->inFormat(new CopyFormat)
                ->save($output);
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            $msg = "ERROR: File $file: $command $errorLog";
            Log::error($msg);
            return false;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }

        $media->cleanupTemporaryFiles();
        $o = $disk->path($output);
        $filesize = File::size($o);
        File::delete($o);

        return $filesize;
    }

    /**
     * @param $from
     * @param $to
     * @param  string  $type
     * @return array
     */
    public function getDatesByRange($from, $to, string $type = 'day'): array
    {
        $period = CarbonPeriod::create($from, "1 {$type}", $to);
        $dates = [];
        $format = static::getDateFormatter($type);

        // Iterate over the period
        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }

        return $dates;
    }

    /**
     * @param  string  $type
     * @return string
     */
    public static function getDateFormatter(string $type): string
    {
        return constant('self::LOGFILE_DATE_' . Str::upper($type));
    }
}
