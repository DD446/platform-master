<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AudiotakesPodcasterTransferTrend;
use App\Nova\Metrics\AveragePayment;
use App\Nova\Metrics\AverageSpendings;
use App\Nova\Metrics\CustomerLifetimeValue;
use App\Nova\Metrics\RevenueToday;
use App\Nova\Metrics\SumFundsDeletedAccounts;
use App\Nova\Metrics\SumOpenFunds;
use Laravel\Nova\Dashboard;
use App\Nova\Metrics\LifetimeRevenue;
use App\Nova\Metrics\PaymentAverage;
use App\Nova\Metrics\PaymentAveragePerMonth;
use App\Nova\Metrics\PaymentsPerMonth;
use App\Nova\Metrics\RevenuePerDay;
use App\Nova\Metrics\RevenuePerMonth;
use App\Nova\Metrics\SumUnpaidBills;
use App\Nova\Metrics\UnpaidBills;

class Payments extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new RevenueToday,
            new RevenuePerDay,
            new RevenuePerMonth,
            /*new RevenuePerYear,*/
            new PaymentsPerMonth,
            new PaymentAveragePerMonth,
            new PaymentAverage,
            new AveragePayment,
            new UnpaidBills,
            new SumUnpaidBills,
            new LifetimeRevenue,
            //new FundsSpentPerMonth, // TODO: Custom implementation needed
            new SumFundsDeletedAccounts,
            new SumOpenFunds,
//            new AverageSpendings,
//            new CustomerLifetimeValue,
            new AudiotakesPodcasterTransferTrend,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'payments';
    }
}
