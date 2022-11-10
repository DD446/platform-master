<?php
/**
 * User: fabio
 * Date: 21.09.22
 * Time: 11:17
 */

namespace App\Classes\Statistics;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class CombinedStatistics
{
    private string $username;

    /**
     * @param  string  $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function merge($legacyData, $realtimeData)
    {
        // TODO
        return $legacyData + $realtimeData;
    }

    /**
     * @param  array  $range
     * @param  string  $type
     * @param  string|null  $feed
     * @param  string|null  $episode
     * @param  string|null  $source
     * @param  string|null  $splitBy
     * @param  array|null  $page
     * @return array
     */
    public function getListeners(array $range, string $type = 'day', ?string $feed = null,  ?string $episode = null, ?string $source = null, ?string $splitBy = null, ?array $page = ['size' => 10, 'number' => 1]): array
    {
        $legacy = new LegacyStatistics($this->username);
        $legacyData = $legacy->listener($range, $type, $feed, $episode, $source, $splitBy, $page);

        $realtime = new RealtimeStatistics($this->username);
        $realtimeData = $realtime->listener($range, $type, $feed, $episode, $source, $splitBy, $page);

        return $this->merge($legacyData, $realtimeData);
    }

    /**
     * @deprecated
     * @param $range
     * @return array
     * @throws \Exception
     */
    private function getRange($range): array
    {
        $prevDates = null;

        if (is_array($range)) {
            if (!array_key_exists('df', $range)) {
                throw new \InvalidArgumentException('Missing key "df".');
            }
            if (!array_key_exists('dt', $range)) {
                throw new \InvalidArgumentException('Missing key "dt');
            }
            $dates = $range;
        } else {
            switch (Str::lower($range)) {
                case 'today':
                    $today = now();
                    $dates = [
                        'df' => $today->startOfDay(),
                        'dt' => $today->endOfDay()
                    ];
                    $prevDates = [
                        'df' => CarbonImmutable::now()->subDays(7)->startOfDay(),
                        'dt' => CarbonImmutable::now()->subDays(7)->endOfDay()];
                    break;
                case 'yesterday':
                    $yesterday = now()->subDay();
                    $dates = [
                        'df' => $yesterday->startOfDay(),
                        'dt' => $yesterday->endOfDay()
                    ];
                    $prevDates = [
                        'df' => CarbonImmutable::now()->subDays(8)->startOfDay(),
                        'dt' => CarbonImmutable::now()->subDays(8)->endOfDay()
                    ];
                    break;
                case 'last7days':
                case 'last30days':
                    // TODO: Change for elastic
                    if ($range === 'last7days') {
                        $firstPeriod = '-8 days';
                        $secondPeriod = '-15 days';
                        $recur = 7;
                    } elseif ($range === 'last30days') {
                        $firstPeriod = '-31 days';
                        $secondPeriod = '-61 days';
                        $recur = 30;
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

                    // TODO
                    break;
                case 'alltime':
                    $date = new \Carbon\Carbon('2011-09-30');
                    $dates = [
                        'df' => $date->startOfDay(),
                        'dt' => now()
                    ];
                    $prevDates = $dates;
                    break;
                default:
                    throw new \Exception('Unknown or missing range.');
            }
        }

        return [
            'dates' => $dates,
            'prevDates' => $prevDates
        ];
    }
}
