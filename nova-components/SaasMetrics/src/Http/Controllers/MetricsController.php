<?php
/**
 * User: fabio
 * Date: 04.07.19
 * Time: 11:07
 */

namespace App\SaasMetrics\Http\Controllers;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VisualAppeal\Matomo;

class MetricsController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $matomo = new Matomo(config('matomotracker.url'), config('matomotracker.tokenAuth'), config('matomotracker.idSite'));

        $results = [];
        $prevVisitors = null;
        $prevTotalNewSignups = null;
        $totalNewSignups = 0;

        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->startOfMonth();

            if ($key->greaterThan(CarbonImmutable::createFromDate('2021', 8))) {
                $matomo->setPeriod(Matomo::PERIOD_MONTH);
                $matomo->setDate($key);
                $visits = $matomo->getUniqueVisitors();
                $results[$key->format('m Y')]['visitors'] = $visits;
                $signups = User::withTrashed()
                    ->customer()
                    ->whereDate('created_at', '<', $key)
                    ->whereNotIn('package_id', [0, 5])
                    ->count();
                $results[$key->format('m Y')]['signups'] = $signups;

                if ($prevVisitors) {
                    $results[$key->format('m Y')]['mmgrowthvisitors'] = round(($visits/$prevVisitors-1)*100, 2);
                } else {
                    $results[$key->format('m Y')]['mmgrowthvisitors'] = null;
                }
                $prevVisitors = $visits;

                if ($prevTotalNewSignups) {
                    $totalNewSignups = $signups - $prevTotalNewSignups;
                    $results[$key->format('m Y')]['totalnewsignups'] = $totalNewSignups;
                    $results[$key->format('m Y')]['mmgrowthnewsignups'] = round(($signups/$prevTotalNewSignups-1)*100, 2);
                } else {
                    $results[$key->format('m Y')]['totalnewsignups'] = null;
                    $results[$key->format('m Y')]['mmgrowthnewsignups'] = null;
                }

                if ($visits) {
                    $results[$key->format('m Y')]['visitortosignupconversionrate'] = round($totalNewSignups / $visits * 100, 2);
                } else {
                    $results[$key->format('m Y')]['visitortosignupconversionrate'] = null;
                }

                $prevTotalNewSignups = $signups;
            }
        }

        return response()->json($results);
    }

    public function getPayingCustomers()
    {
        $results = [];

        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->startOfMonth();
            $j = $i + 1;
            $prev = now()->subMonths($j)->startOfMonth();

            //if ($key->greaterThan(CarbonImmutable::createFromDate('2021', 8))) {
                $customersStartOfMonth = \App\Models\User::withTrashed()
                    ->customer()
                    ->where('is_trial', '=', User::HAS_PAID)
                    ->whereDate('created_at', '<=', $key)
                    ->where(function($query) use ($key) {
                        return $query->whereDate('deleted_at', '>', $key)
                            ->orWhereNull('deleted_at');
                    })
                    ->count();
                $results[$key->format('m Y')]['customersstartmonth'] = $customersStartOfMonth;

                if (!isset($prevCustomersEndOfMonth)) {
                    $prevCustomersEndOfMonth = $customersStartOfMonth;
                }

                $newCustomers = \App\Models\User::withTrashed()
                    ->customer()
                    ->where('is_trial', '=', User::HAS_PAID)
                    ->whereBetween('created_at', [$prev, $key])
                    ->where('package_id', '!=', 5)
                    ->where(function($query) use ($key) {
                        return $query->whereDate('deleted_at', '>', $key)
                            ->orWhereNull('deleted_at');
                    })
                    ->count();

                $results[$key->format('m Y')]['newcustomers'] = $newCustomers;

                $totalNewSignups = \App\Models\User::withTrashed()
                    ->customer()
                    ->whereBetween('created_at', [$prev, $key])
                    ->where('package_id', '!=', 5)
                    ->count();

                $results[$key->format('m Y')]['totalnewsignups'] = $totalNewSignups;
                $results[$key->format('m Y')]['conversionrate'] = $totalNewSignups > 0 ? round(($newCustomers / $totalNewSignups)*100, 2) : $totalNewSignups;

                $lostCustomers = \App\Models\User::withTrashed()
                    ->customer()
                    ->where('is_trial', '=', User::HAS_PAID)
                    ->where('package_id', '!=', 5)
                    ->whereBetween('deleted_at', [$prev, $key])
                    ->count();

                $results[$key->format('m Y')]['lostcustomers'] = $lostCustomers;
                $results[$key->format('m Y')]['churnrate'] = round(($lostCustomers / $customersStartOfMonth)*100, 2);
                $netNewCustomers = $newCustomers - $lostCustomers;
                $results[$key->format('m Y')]['netnewcustomers'] = $netNewCustomers;
                $customersEndOfMonth = $customersStartOfMonth + $netNewCustomers;
                $results[$key->format('m Y')]['customersendmonth'] = $customersEndOfMonth;
                $results[$key->format('m Y')]['mmgrowthcustomers'] = round (($customersEndOfMonth / $prevCustomersEndOfMonth-1)*100,2);
                $prevCustomersEndOfMonth = $customersEndOfMonth;
            //}
        }

        return response()->json($results);
    }
}

// UPDATE usr SET is_trial=-2 WHERE usr_id IN (SELECT DISTINCT usr_id FROM `user_accounting` WHERE `activity_type` = 1 AND activity_characteristic>1);
