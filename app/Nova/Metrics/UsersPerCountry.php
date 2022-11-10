<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use App\Models\User;

class UsersPerCountry extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $countrylist =  \Countrylist::getList('de', 'php');

        return $this->count($request, User::where('role_id', '=', User::ROLE_USER)->where('country', '!=', '')->orderBy('aggregate', 'DESC'), 'country')
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
        return now()->addMinutes(60);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'users-per-country';
    }
}
