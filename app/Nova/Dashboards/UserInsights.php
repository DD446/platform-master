<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\CustomerLifetimeValue;
use App\Nova\Metrics\DeletedUsersPerDay;
use App\Nova\Metrics\MemberTrend;
use App\Nova\Metrics\PackageChangeDeletionTrend;
use App\Nova\Metrics\PackageChangeDowngradeTrend;
use App\Nova\Metrics\PackageChangeUpgradeTrend;
use App\Nova\Metrics\TrialUsersCount;
use App\Nova\Metrics\UserDpasPerDay;
use App\Nova\Metrics\UsersPerCityInAustria;
use App\Nova\Metrics\UsersPerCityInGermany;
use App\Nova\Metrics\UsersPerCityInSwitzerland;
use App\Nova\Metrics\UsersPerPostCodeInAustria;
use App\Nova\Metrics\UsersPerPostCodeInGermany;
use App\Nova\Metrics\UsersPerPostCodeInSwitzerland;
use Laravel\Nova\Dashboard;
use App\Nova\Metrics\AllUsers;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UsersPerCountry;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\UsersPerMonth;
use App\Nova\Metrics\UsersPerPlan;

class UserInsights extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new UsersPerDay,
            new NewUsers,
            new UsersPerMonth,
            new AllUsers,
            new UsersPerPlan,
            new UsersPerCountry,
            new UsersPerCityInGermany,
            new UsersPerPostCodeInGermany,
            new UsersPerCityInAustria,
            new UsersPerPostCodeInAustria,
            new UsersPerCityInSwitzerland,
            new UsersPerPostCodeInSwitzerland,
            new TrialUsersCount,
            new MemberTrend,
            new DeletedUsersPerDay,
            new PackageChangeUpgradeTrend,
            new PackageChangeDowngradeTrend,
            new PackageChangeDeletionTrend,
            new UserDpasPerDay,
//            new CustomerLifetimeValue,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'user-insights';
    }
}
