<?php

namespace App\Nova\Metrics;

use App\Classes\Activity;
use App\Models\UserAccounting;
use App\Models\UserPayment;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class AverageSpendings extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->average($request, UserAccounting::where(function($query) {
            return $query->where('activity_type', '=', Activity::PACKAGE)
                ->orWhere('activity_type', '=', Activity::EXTRAS);
        }), DB::raw('ROUND(SUM(amount)/COUNT(DISTINCT usr_id),2)'))
            ->currency('€');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
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
        return 'average-monthly-spendings';
    }
}
