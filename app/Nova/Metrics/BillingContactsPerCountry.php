<?php

namespace App\Nova\Metrics;

use App\Models\UserBillingContact;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class BillingContactsPerCountry extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $countrylist =  \Countrylist::getList('de', 'php');

        return $this->count($request, UserBillingContact::where('country', '!=', '')->orderBy('aggregate', 'DESC'), 'country')
            ->label(function ($value) use ($countrylist) {
                if (array_key_exists($value, $countrylist)) {
                    return $countrylist[$value];
                }
                return 'Unknown';
            });
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addDay();
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'billing-contacts-per-country';
    }
}
