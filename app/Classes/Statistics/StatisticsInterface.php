<?php
/**
 * User: fabio
 * Date: 06.10.22
 * Time: 09:22
 */

namespace App\Classes\Statistics;

interface StatisticsInterface
{
    public function listener(array $range, string $type = 'day', ?string $feed = null,  ?string $episode = null, ?string $source = null, ?string $splitBy = null, ?array $page = ['size' => 10, 'number' => 1]): array;
}
