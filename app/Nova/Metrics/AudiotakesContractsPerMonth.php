<?php

namespace App\Nova\Metrics;

use App\Models\AudiotakesContract;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class AudiotakesContractsPerMonth extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->countByMonths($request, AudiotakesContract::whereNotNull('feed_id'), 'audiotakes_date_accepted');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            12 => '12 Month',
            6 => '6 Month',
            3 => '3 Month',
            1 => '1 Month',
            24 => '24 Month',
            36 => '36 Month',
            48 => '48 Month',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'audiotakes-contracts-per-month';
    }
}
