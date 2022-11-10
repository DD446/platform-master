<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use App\Models\User;

class UsersPerPlan extends Partition
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
            ->count($request, User::customer()->where('package_id', '>', 0), 'package_id')
            ->label(function ($value) {
                switch ($value) {
/*                    case 0:
                        return 'None';*/
                    case 1:
                        return 'Starter';
                    case 2:
                        return 'Podcaster';
                    case 3:
                        return 'Profi';
                    case 4:
                        return 'Maxi';
                    case 5:
                        return 'Presse';
                    case 6:
                        return 'Corporate';
                    case 7:
                        return 'Agency';
                    default:
                        return $value;
                }
            });
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'users-per-plan';
    }
}
