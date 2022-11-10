<?php

/**
 * Date: Tue, 25 Sep 2018 20:42:44 +0000.
 */

namespace App\Models;

use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Voucher
 *
 * @property int $id
 * @property int $voucher_action_id
 * @property int $usage_count
 * @property \Carbon\Carbon $valid_until
 * @property string $voucher_code
 * @property string $comment
 * @property int $valid_for
 * @property int $amount
 * @property int $amount_per_person
 * @property \Carbon\Carbon $date_created
 *
 * @package App\Models
 */
class Voucher extends Eloquent
{
    const ACTION_TYPE_ONE = 1;

	protected $casts = [
		'voucher_action_id' => 'int',
		'usage_count' => 'int',
		'valid_for' => 'int',
		'amount' => 'int',
		'amount_per_person' => 'int'
	];

	protected $dates = [
		'valid_until',
		'date_created'
	];

	protected $fillable = [
        'voucher_action_id',
		'usage_count',
		'valid_until',
		'voucher_code',
		'comment',
		'valid_for',
		'amount',
		'amount_per_person',
		'date_created'
	];

    protected static function boot()
    {
        static::updated(function(Voucher $voucher) {
        });

        parent::boot();
    }

    public function setValidUntilAttribute($value)
    {
        $this->attributes['valid_until'] = Carbon::parse($value);
    }

    public function setDateCreatedAttribute($value)
    {
        $this->attributes['date_created'] = Carbon::parse($value);
    }

    public function voucher_action()
    {
        return $this->belongsTo(VoucherAction::class);
    }

    public function redeem(User $user)
    {
        DB::beginTransaction();

        try {
            switch ($this->voucher_action->type) {
                case VoucherAction::TYPE_EXTENDED_TRIALPERIOD:
                    if (!$user->isInTrial()) {
                        throw new \Exception(trans('accounting.message_error_voucher_not_applicable_trial_period_ended', ['name' => $this->voucher_code]));
                    }
                    $previousEnd = $user->date_trialend;
                    $user->date_trialend = $user->date_trialend->addDays($this->voucher_action->units);
                    $user->save();
                    $uam = new UserAccountingManager();
                    $uam->add($user, Activity::PACKAGE, $user->package_id, 0,
                        null, UserPayment::CURRENCY_DEFAULT, $previousEnd, $user->date_trialend);
                    break;
                case VoucherAction::TYPE_EXTRA_STORAGE:
                    $uaManager = new UserAccountingManager();
                    $description = trans('package.text_package_extra_description_storage',
                        ['piece' => UserExtra::getPieces()['storage'] * $this->voucher_action->units]) . ' (' . trans('accounting.voucher') . ')';
                    $uaManager->add($user, Activity::VOUCHER, UserAccounting::VOUCHER_ACTIVITY_SPACE, $this->voucher_action->units, $description, UserPayment::CURRENCY_DEFAULT, now(), null, UserPayment::STATUS_CLOSED, UserPayment::PROCEDURE_MANUAL, false, false);
                    break;
                default:
                    throw new \Exception(trans('accounting.message_error_unknown_voucher_action_type'));
            }
            VoucherRedemption::create([
                'voucher_id' => $this->id,
                'user_id' => $user->id,
                'amount' => 1,
            ]);
            $this->increment('usage_count');
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            $username = $user->username;
            Log::error("ERROR: User: {$username}: Redeeming voucher failed: " . $e->getMessage());
            throw new \Exception($e->getMessage());
        }

        return true;
    }
}
