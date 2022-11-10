<?php

namespace App\Nova\Metrics;

use App\Models\UserDpa;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use App\Models\User;

class UserDpasPerDay extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this
            ->countByDays($request, UserDpa::class)
            ->showLatestValue();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => '30 Days',
            'TODAY' => 'Today',
            60 => '60 Days',
            90 => '90 Days',
            180 => '180 Days',
            365 => '365 Days',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addHour();
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'user-dpas-per-day';
    }
}
