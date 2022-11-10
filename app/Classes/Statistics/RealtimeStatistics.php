<?php
/**
 * User: fabio
 * Date: 21.09.22
 * Time: 11:22
 */

namespace App\Classes\Statistics;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class RealtimeStatistics implements StatisticsInterface
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
     * @param  array  $range
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
}
