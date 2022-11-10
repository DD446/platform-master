<?php
/**
 * User: fabio
 * Date: 23.03.20
 * Time: 13:56
 */

namespace App\Classes;

use App\Models\PackageChange;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserExtra;
use App\Models\UserPayment;
use App\Scopes\IsVisibleScope;
use Illuminate\Support\Facades\Log;

class UserAccountingManager
{
    /**
     * @param  UserExtra  $extra
     * @return object
     * @throws \Throwable
     */
    public function trackOrder(UserExtra $extra): object
    {
        $ua = new UserAccounting();
        $ua->usr_id = $extra->user_id;
        $ua->activity_type = \App\Classes\Activity::EXTRAS;
        $ua->activity_characteristic = $extra->extras_type;
        $ua->activity_description = trans('main.text_order_extra_description_detail', ['name' => $extra->extras_description]);
        $ua->amount = change_prefix($extra->extras_count);
        $ua->currency = UserPayment::CURRENCY_DEFAULT;
        $ua->date_created = Carbon::now();
        $ua->date_start = Carbon::now();
        $ua->date_end = Carbon::now()->addDays(30);
        $ua->status = UserPayment::STATUS_CLOSED;
        $ua->procedure = UserPayment::PROCEDURE_AUTO;
        $ua->saveOrFail();

        return $ua;
    }

    /**
     * @param $userId
     * @param $activityType
     * @param $activityCharacteristic
     * @param $amount
     * @param  null  $activityDescription
     * @param  string  $currency
     * @param  null  $dateStart
     * @param  null  $dateEnd
     * @param  int  $status
     * @param  int  $procedure
     * @param  false  $skipNotification
     * @return mixed
     */
    public function add(User $user, $activityType, $activityCharacteristic, $amount, $activityDescription = null,
        $currency = UserPayment::CURRENCY_DEFAULT, $dateStart = null, $dateEnd = null,
        $status = UserPayment::STATUS_CLOSED, $procedure = UserPayment::PROCEDURE_AUTO, $isUpgrade = false, $charge = true)
    {
        if (is_null($dateStart)) {
            $dateStart = Carbon::now();
        } elseif ($dateStart instanceof \DateTime) {
            $dateStart = $dateStart->format('Y-m-d H:i:s');
        }
        $activityDescription = $this->getActivityDescription($activityType, $activityCharacteristic,
            $activityDescription);

        if (is_null($dateEnd)) {
            $dateEnd = $this->getDateEnd($activityType, $activityCharacteristic, $dateStart);
        } elseif ($dateEnd instanceof \DateTime) {
            $dateEnd = $dateEnd->format('Y-m-d H:i:s');
        }

        // $amount should only be null if user is in trial
        // in any case an $amount of 0 never changes anything
        if ($charge) {
            if ($amount <> 0) { // Guthaben setzen
                $user->funds += $amount;// Block-Status ändern
                $user->is_blocked = $user->funds < 0;// Probephase beenden
                $user->is_trial = User::HAS_PAID;// This needs to be above the makeBooking() call so values for notification are set
                $user->saveOrFail();
            }
        }

        return $this->makeBooking($user->user_id, $activityType, $activityCharacteristic, $activityDescription, $amount,
            $currency, $dateStart, $dateEnd, $status, $procedure, $isUpgrade);
    }

    private function getActivityDescription($activityType, $activityCharacteristic, $details = null)
    {
        switch ($activityType) {
            case Activity::REFUND :
                $description = trans('bills.accounting_activity_refunds', ['details' => $details]);
                break;
            case Activity::FUNDS :
                $description = trans_choice('bills.accounting_activity_add_funds_type', $activityCharacteristic);
                break;
            case Activity::PACKAGE :
                $packageName = Package::withoutGlobalScope(IsVisibleScope::class)->whereTld(config('app.domain'))->wherePackageId($activityCharacteristic)->value('package_name');
                $description = ($details ?: trans('bills.accounting_activity_booking', ['details' => trans_choice('package.package_name', $packageName)]));
                break;
            case Activity::EXTRAS :
                $description    = trans('funds.accounting_activity_extras', ['details' => $details]);
                break;
            case Activity::ENCODING :
            default :
                $description = $details;
                break;
        }

        return $description;
    }

    private function getDateEnd($activityType, $activityCharacteristic, $dateStart)
    {
        $date                   = new \DateTime($dateStart);
        $defaultPeriod          = 30;
        $paymentPeriod          = Package::PAYMENT_PERIOD;

        switch ($activityType) {
            case Activity::REFUND :
            case Activity::FUNDS :
                $paymentPeriod  = 12*99; # 99 years
                break;
            case Activity::PACKAGE :
                $paymentPeriod = Package::withoutGlobalScope(IsVisibleScope::class)->whereTld(config('app.domain'))->wherePackageId($activityCharacteristic)->value('paying_rhythm');
                break;
        }
        $period = 'P' . $defaultPeriod * $paymentPeriod . 'D';
        $date->add(new \DateInterval($period));

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param  int  $userId
     * @param  int  $type
     * @param  int  $characteristic
     * @param  string  $description
     * @param  float  $amount
     * @param  string  $currency
     * @param $dateStart
     * @param $dateEnd
     * @param $status
     * @param  int  $procedure
     * @return UserAccounting
     * @throws \Throwable
     */
    private function makeBooking(int $userId, int $type, int $characteristic, string $description, float $amount, string $currency,
        $dateStart, $dateEnd, $status, $procedure = UserPayment::PROCEDURE_AUTO, $isUpgrade = false): UserAccounting
    {
        $ua = new UserAccounting();
        $ua->usr_id = $userId;
        $ua->activity_type = $type;
        $ua->activity_characteristic = $characteristic;
        $ua->activity_description = $description;
        $ua->amount = $amount;
        $ua->currency = $currency;
        $ua->date_created = now();
        $ua->date_start = $dateStart;
        $ua->date_end = $dateEnd;
        $ua->status = $status;
        $ua->procedure = $procedure;
        $ua->is_upgrade = $isUpgrade;
        $ua->saveOrFail();

        return $ua;
    }

    public function getDueCustomers(int $limit = 15): array
    {
        $now = Carbon::now();
        $users = "
            SELECT
                user_accounting.usr_id, user_accounting.date_end
            FROM
                user_accounting
                RIGHT JOIN usr ON (user_accounting.usr_id = usr.usr_id)
            WHERE
                accounting_id IN(
                SELECT
                    MAX(accounting_id)
                FROM
                    `user_accounting`
                WHERE
                    `activity_type` = :at
                GROUP BY
                    (usr_id)
            )
            AND `user_accounting`.date_end < :dt
            AND `usr`.is_acct_active = 1
            AND `usr`.role_id = :rl
            AND `usr`.package_id > 0
            AND `usr`.deleted_at IS NULL
            ORDER BY `user_accounting`.`usr_id` ASC
            LIMIT :lt
        ";

        return \DB::select($users, ['rl' => User::ROLE_USER, 'at' => Activity::PACKAGE, 'dt' => $now, 'lt' => $limit]);
    }

    /**
     * @param  UserAccounting  $order
     * @return int
     * @throws \Exception
     */
    public function getRefund(UserAccounting $order): float
    {
        $dateStart      = new \DateTime($order->date_start);
        $dateToday      = new \DateTime();
        $dateInterval   = $dateStart->diff($dateToday);
        $periodEnd      = new \DateTime($order->date_end);
        $periodInterval = $dateStart->diff($periodEnd);
        $pAmount        = change_prefix($order->amount);

        return round($pAmount-($pAmount/$periodInterval->format('%a'))*$dateInterval->format('%a'), 2);
    }

    /**
     * @param  int  $userId
     * @return object
     */
    public function getLastActivePackageOrder(int $userId): ?UserAccounting
    {
        // Hole letzte Buchung für User/Paket
        return UserAccounting::whereUsrId($userId)
            ->whereActivityType(Activity::PACKAGE)
            ->where('date_end', '>', now())
            ->orderByDesc('accounting_id')
            ->first();
    }

    /**
     * @param  User  $user
     * @param  int  $packageId
     * @return bool
     */
    public function changePackage(User $user, int $packageId): bool
    {
        $now = now();
        $res = false;

        DB::beginTransaction();

        try {
            // Berücksichtige bei Berechnung Guthaben, was zurückerstattet wird
            $lastOrder = $this->getLastActivePackageOrder($user->user_id);
            $refunds = 0;
            $newPackage = Package::withoutGlobalScope(IsVisibleScope::class)->whereTld(config('app.domain'))->wherePackageId($packageId)->first();// Falls der Benutzer für seine letzte Buchung etwas bezahlt hat,
            // berechne den Betrag, den der Benutzer anteilig zurückerhält
            if ($lastOrder && $lastOrder->amount < 0) {
                $refunds = $this->getRefund($lastOrder);
            }
            $oldPackageName = trans_choice('package.package_name', $user->package->package_name);
            $newPackageName = trans_choice('package.package_name', $newPackage->package_name);
            $activityDescription = trans('package.text_activity_description_package_change', [
                'oldPackageName' => $oldPackageName, 'newPackageName' => $newPackageName
            ]);

            // If there is something to refund, refund it
            if ($refunds > 0) {
                $this->add($user, Activity::REFUND, UserAccounting::REFUND_PACKAGE,
                    $refunds, $activityDescription, $lastOrder->currency, $now, null, UserPayment::STATUS_CLOSED,
                    UserPayment::PROCEDURE_MANUAL);
            }

            // Charge package if user left trial period
            if (now()->greaterThan(CarbonImmutable::createFromTimestamp(strtotime($user->date_trialend)))
                || !$user->isInTrial()) {
                $this->add($user, Activity::PACKAGE, $newPackage->package_id,
                    change_prefix($newPackage->monthly_cost * $newPackage->paying_rhythm), null,
                    UserPayment::CURRENCY_DEFAULT, $now, null, UserPayment::STATUS_CLOSED,
                    UserPayment::PROCEDURE_MANUAL, true);
            } elseif (now()->lessThan(CarbonImmutable::createFromTimestamp(strtotime($user->date_trialend)))) {
                // User can do free upgrades during trial (period)
                $this->add($user, Activity::PACKAGE, $newPackage->package_id, 0, null,
                    UserPayment::CURRENCY_DEFAULT, $now, null, UserPayment::STATUS_CLOSED,
                    UserPayment::PROCEDURE_MANUAL, $newPackage->package_id > $user->package->package_id);
            }

            PackageChange::create([
                'user_id' => $user->id,
                'type' => $newPackage->package_id > $user->package_id ? PackageChange::TYPE_UPGRADE : PackageChange::TYPE_DOWNGRADE,
                'from' => $user->package_id,
                'to' => $newPackage->package_id,
            ]);

            // Change user package
            $user->package_id = $newPackage->package_id;
            $user->new_package_id = null;
            $res = $user->save();
            DB::commit();
        } catch (\Exception $e) {
            $username = $user->username;
            Log::error("ERROR: User: {$username}: Changing package failed: " . $e->getMessage());
            DB::rollBack();
        }


        return $res;
    }
}
