<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\SupporterCount;
use Laravel\Nova\Dashboard;

class Agency extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new SupporterCount,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'agency';
    }
}
