<?php

/**
 * Date: Fri, 24 Aug 2018 14:26:51 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Notifications\ChargeNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use App\Classes\Activity;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Notifications\ExtraChargeNotification;
use App\Notifications\FundsNotification;
use App\Notifications\RefundNotification;

/**
 * Class UserAccounting
 *
 * @property int $accounting_id
 * @property int $usr_id
 * @property int $activity_type
 * @property int $activity_characteristic
 * @property string $activity_description
 * @property float $amount
 * @property string $currency
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $date_start
 * @property \Carbon\Carbon $date_end
 * @property bool $procedure
 * @property bool $status
 *
 * @package App\Models
 */
class UserAccounting extends Eloquent
{
    use Notifiable, HasFactory;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'date_created';

    const PROCEDURE_MANUAL  = 1;
    const PROCEDURE_AUTO    = 2;
    const STATUS_UNPAID     = -1;
    const STATUS_OPEN       = 0;
    const STATUS_CLOSED     = 1;
    const REFUND_PACKAGE    = 1;
    const AUDIOTAKES_TRANSFER_FUNDS = 6;
    const VOUCHER_ACTIVITY_SPACE = 1;

	protected $table = 'user_accounting';
	protected $primaryKey = 'accounting_id';

	public $timestamps = false;

	public $is_upgrade = false;

	protected $casts = [
		'accounting_id' => 'int',
		'usr_id' => 'int',
		'activity_type' => 'int',
		'activity_characteristic' => 'int',
		'amount' => 'float',
		'procedure' => 'bool',
		'status' => 'bool'
	];

	protected $dates = [
		'date_created',
		'date_start',
		'date_end'
	];

	protected $fillable = [
		'usr_id',
		'activity_type',
		'activity_characteristic',
		'activity_description',
		'amount',
		'currency',
		'date_created',
		'date_start',
		'date_end',
		'procedure',
		'status'
	];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::created(function(UserAccounting $userAccounting) {
            switch ($userAccounting->activity_type) {
                case Activity::REFUND:
                    $userAccounting->notifyNow(new RefundNotification());
                    break;
                case Activity::FUNDS:
                    $userAccounting->notify(new FundsNotification());
                    break;
                case Activity::PACKAGE:
                    $userAccounting->bookSpace($userAccounting);
                    // Prevent mail server overload when running cron job to charge users
                    $delay = now()->addSeconds(rand(0,960));
                    $userAccounting->notify((new ChargeNotification())->delay($delay));
                    break;
                case Activity::EXTRAS:
                    $userAccounting->notify(new ExtraChargeNotification());

                    if ($userAccounting->activity_characteristic == UserExtra::EXTRA_STORAGE) {
                        // Space needs user_accounting_id so this cannot move to UserExtra!
                        $space = new Space();
                        $space->user_id = $userAccounting->user_id;
                        $space->user_accounting_id = $userAccounting->id;
                        $space->type = Space::TYPE_EXTRA;
                        $space->created_at = $userAccounting->date_start;
                        $space->space = UserExtra::DEFAULT_STORAGE * (0-$userAccounting->amount);
                        $space->space_available = UserExtra::DEFAULT_STORAGE * (0-$userAccounting->amount);
                        $space->is_available = true;
                        $space->save();
                    }
                    break;
                case Activity::ENCODING:
                    break;
                case Activity::VOUCHER:
                    if ($userAccounting->activity_characteristic == UserAccounting::VOUCHER_ACTIVITY_SPACE) {
                        $space = new Space();
                        $space->user_id = $userAccounting->user_id;
                        $space->user_accounting_id = $userAccounting->id;
                        $space->type = Space::TYPE_VOUCHER;
                        $space->created_at = $userAccounting->date_start;
                        $space->space = UserExtra::DEFAULT_STORAGE * $userAccounting->amount;
                        $space->space_available = UserExtra::DEFAULT_STORAGE * $userAccounting->amount;
                        $space->is_available = true;
                        $space->save();
                    }
                    break;
            }
        });
    }

    public function scopeOwner($query)
    {
        return $query->where('usr_id', '=', auth()->id());
    }

    public function scopeRefunds()
    {
        return $this->where('activity_type', '=', Activity::REFUND);
    }

    public function scopeFunds()
    {
        return $this->where('activity_type', '=', Activity::FUNDS);
    }

    public function scopePackage()
    {
        return $this->where('activity_type', '=', Activity::PACKAGE);
    }

    public function scopeEncoding()
    {
        return $this->where('activity_type', '=', Activity::ENCODING);
    }

    public function scopeExtras()
    {
        return $this->where('activity_type', '=', Activity::EXTRAS);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_id', 'usr_id');
    }

    public function routeNotificationForMail()
    {
        return $this->user->userbillingcontact->email ?? $this->user->email;
    }

    public function getIdAttribute()
    {
        return $this->accounting_id;
    }

    public function getUserIdAttribute()
    {
        return $this->usr_id;
    }

    public function setCurrency($value)
    {
        $this->attributes['currency'] = Str::uppercase($value);
    }

    /**
     * Helper for package management
     * Adds new space on new billing period
     * or upgrades
     *
     * @param  UserAccounting  $ua
     * @return void
     * @throws \Throwable
     */
    private function bookSpace(UserAccounting $ua)
    {
        $usedSpace = 0;
        // Substract used space from newly available (total) space
        if ($ua->is_upgrade) {
            $prevSpace = Space::available()->whereUserId($ua->user_id)->whereType(Space::TYPE_REGULAR)->latest()->first();
            $usedSpace = $prevSpace->space - $prevSpace->space_available;
        }

        $package = Package::withoutGlobalScopes()->wherePackageId($ua->activity_characteristic)->firstOrFail();
        $storage = (int) get_package_units($package, Package::FEATURE_STORAGE);
        $space = new Space();
        $space->user_id = $ua->user_id;
        $space->user_accounting_id = $ua->accounting_id;
        $space->type = Space::TYPE_REGULAR;
        $space->created_at = $ua->date_start;
        $space->space = $storage;
        $space->space_available = $storage - $usedSpace;
        $space->is_available = true;
        $space->saveOrFail();
    }

    public function getDateCreatedAttribute($date)
    {
        return Carbon::parse($date)->timezone(config('app.timezone'));
    }
}
