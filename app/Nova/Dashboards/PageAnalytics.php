<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;

class PageAnalytics extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new \Rocramer\MatomoAnalytics\Cards\UniqueVisitors(),
            new \Rocramer\MatomoAnalytics\Cards\Visits(),
            new \Rocramer\MatomoAnalytics\Cards\VisitLength(),
            new \Rocramer\MatomoAnalytics\Cards\BounceRate(),
            new \Rocramer\MatomoAnalytics\Cards\Outlinks(),
            new \Rocramer\MatomoAnalytics\Cards\Downloads(),
            new \Rocramer\MatomoAnalytics\Cards\EntryPages(),
            new \Rocramer\MatomoAnalytics\Cards\ExitPages(),
            new \Rocramer\MatomoAnalytics\Cards\MostViewedPages(),
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'page-analytics';
    }
}
