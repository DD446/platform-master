<?php
/**
 * User: fabio
 * Date: 17.11.21
 * Time: 10:27
 */

namespace App\Classes;

use App\Models\AudiotakesBankTransfer;
use App\Models\AudiotakesContract;
use App\Models\AudiotakesPayout;
use App\Models\AudiotakesPodcasterTransfer;
use App\Models\UserAccounting;
use App\Models\UserPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AudiotakesManager
{
    /**
     * @param  float  $rawRevenue
     * @param  float  $holdback
     * @param  float  $share
     * @param  float  $preshare
     * @return float
     */
    public static function calculateFunds(float $rawRevenue, float $share,
        float $preshare = AudiotakesContract::GENERAL_SUBSTRACTION,
        float $holdback = AudiotakesContract::PLATFORM_HOLDBACK): float
    {
        return round($rawRevenue * (1-$holdback/100) * (1-$preshare/100) * $share/100, 2, PHP_ROUND_HALF_DOWN);
    }

    /**
     * @param  int  $userId
     * @return float
     */
    public static function getFunds(int $userId)
    {
        return round(AudiotakesPayout::whereUserId($userId)->sum('funds_open'), PHP_ROUND_HALF_DOWN);
    }

    public static function transferFunds(?\Illuminate\Contracts\Auth\Authenticatable $user, float $payoutFunds)
    {
        // Check again that
        if ($payoutFunds > self::getFunds($user->id)) {
            throw new \OutOfRangeException(trans('audiotakes.error_message_payout_funds_higher_than_available_funds'));
        }

        DB::beginTransaction();

        try {
            // 1. Save as package funds
            // 2. Subtract from ad earnings
            // 3. ? Log process
            // 4. Create billing for audiotakes
            $uam = new UserAccountingManager();
            $activityDescription = trans('package.');

            $uam->add($user, Activity::FUNDS, UserAccounting::AUDIOTAKES_TRANSFER_FUNDS,
                $payoutFunds, $activityDescription, AudiotakesContract::DEFAULT_CURRENCY, null, null, UserPayment::STATUS_CLOSED,
                    UserPayment::PROCEDURE_MANUAL);

            AudiotakesPodcasterTransfer::create(['user_id' => $user->id, 'funds' => $payoutFunds]);
            self::substractFunds($user->id, $payoutFunds);
            DB::commit();
        } catch (\Exception $e) {
            $username = $user->username;
            Log::error("ERROR: User: $username: Transferring funds failed: " . $e->getMessage());
            DB::rollBack();
        }


        return self::getFunds($user->id);
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  float  $payoutFunds
     * @param  int  $payoutGoal
     * @return float
     */
    public static function payoutFunds(\Illuminate\Contracts\Auth\Authenticatable $user, float $payoutFunds, int $payoutGoal, int $contractPartnerId): float
    {
        // Check again that
        if ($payoutFunds > self::getFunds($user->id)) {
            throw new \OutOfRangeException(trans('audiotakes.error_message_payout_funds_higher_than_available_funds'));
        }

        DB::beginTransaction();

        try {
            AudiotakesBankTransfer::create([
                'user_id' => $user->id,
                'audiotakes_payout_contact_id' => $payoutGoal,
                'audiotakes_contract_partner_id' => $contractPartnerId,
                'funds' => $payoutFunds,
            ]);
            self::substractFunds($user->id, $payoutFunds);
            DB::commit();
        } catch (\Exception $e) {
            $username = $user->username;
            Log::error("ERROR: {$username}: Creating an audiotakes bank transfer failed: " . $e->getMessage());
            DB::rollBack();
        }

        return self::getFunds($user->id);
    }

    private static function substractFunds(int $userId, float $payoutFunds)
    {
        $openFunds = AudiotakesPayout::where('user_id', '=', $userId)->where('funds_open', '>', 0)->orderBy('created_at')->get();
        $openFund = $openFunds->pop();

        while($payoutFunds > 0) {
            $remaining = $payoutFunds - $openFund->funds_open;
            $substractFunds = $payoutFunds;

            if ($substractFunds > $openFund->funds_open) {
                $substractFunds = $openFund->funds_open;
            }
            $openFund->decrement('funds_open', $substractFunds);
            $payoutFunds = $remaining;
            $openFund = $openFunds->pop();
        }
    }
}
// = 100 * (1-10/100) * (1-35/100) * (70/100) = 40,95
