<?php

namespace App\Nova\Metrics;

use App\Models\AudiotakesPayout;
use App\Models\AudiotakesPodcasterTransfer;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class AudiotakesPodcasterTransferTotal extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->sum($request, AudiotakesPodcasterTransfer::class, 'funds')->currency('€');
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
     //   return now()->addHours(24);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'audiotakes-podcaster-transfer-total';
    }
}
