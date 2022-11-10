<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AudiotakesPioniereCount;
use App\Nova\Metrics\AudiotakesPodcasterTransferTrend;
use App\Nova\Metrics\ContactUsCount;
use App\Nova\Metrics\FeedbackCount;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\Revenue;
use App\Nova\Metrics\RevenueToday;
use App\Nova\Metrics\ReviewCount;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\UserUploadCount;
use App\Nova\Metrics\VoucherRedemptionCount;
use App\Nova\Metrics\VoucherRedemptionTrend;
use Laravel\Nova\Dashboard;

class AdminInsights extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new Revenue,
            new RevenueToday,
            new UsersPerDay,
            new NewUsers,
            new ContactUsCount,
            new FeedbackCount,
            new ReviewCount,
            new UserUploadCount,
            new AudiotakesPioniereCount,
            new AudiotakesPodcasterTransferTrend,
            new VoucherRedemptionTrend,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'admin-insights';
    }
}
