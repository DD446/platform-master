<?php
/**
 * User: fabio
 * Date: 21.09.22
 * Time: 11:20
 */

namespace App\Classes\Statistics;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class LegacyStatistics implements StatisticsInterface
{
    private string $username;

    /**
     * @param  string  $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @param $range
     * @param  string  $type
     * @param  string|null  $source
     * @param  string|null  $splitBy
     * @param  array|null  $page
     * @return array
     */
    public function listener(array $range, string $type = 'day', ?string $feed = null,  ?string $episode = null, ?string $source = null, ?string $splitBy = null, ?array $page = ['size' => 10, 'number' => 1]): array
    {
        return [];
    }

    /**
     * @param  array  $dateRange
     * @param  string  $type
     * @param  string|null  $source
     * @return array
     */
    public function subscriber($range, string $type = 'day', ?string $source = null): array
    {
        return [];
    }

    /**
     * @param $range
     * @return array
     * @throws \Exception
     */
    private function getRange($range): array
    {
        $dates = null;
        $prevDates = null;

        if (is_array($range)) {
            if (!array_key_exists('df', $range)) {
                throw new \InvalidArgumentException('Missing key "df".');
            }
            if (!array_key_exists('dt', $range)) {
                throw new \InvalidArgumentException('Missing key "dt');
            }
        } else {
            switch (Str::lower($range)) {
                case 'today':
                    $today = new \DateTimeImmutable('today');
                    $dates = [$today->format('d-M-Y')];
                    $prevDates = [CarbonImmutable::now()->subDays(7)->format('d-M-Y')];
                    break;
                case 'yesterday':
                    $yesterday = new \DateTimeImmutable('yesterday');
                    $dates = [$yesterday->format('d-M-Y')];
                    $prevDates = [CarbonImmutable::now()->subDays(8)->format('d-M-Y')];
                    break;
                case 'last7days':
                case 'last30days':
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
            'prevDates' => $prevDates
        ];
    }
}
