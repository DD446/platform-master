<?php

/**
 * Date: Fri, 24 Aug 2018 10:24:43 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Notifications\UserPaymentNotification;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Class UserPayment
 *
 * @property int $payment_id
 * @property int $receiver_id
 * @property int $payer_id
 * @property int $usr_id
 * @property float $amount
 * @property string $currency
 * @property int $payment_method
 * @property \Carbon\Carbon $date_created
 * @property string $bill_id
 * @property bool $is_refundable
 * @property string $comment
 *
 * @package App\Models
 */
class UserPayment extends Eloquent
{
    use Notifiable, SoftDeletes;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'date_created';

    const PAYMENT_METHOD_VOUCHER    = 1;
    const PAYMENT_METHOD_LOCALBANK  = 2;
    const PAYMENT_METHOD_FOREIGNBANK= 3;
    const PAYMENT_METHOD_PAYPAL     = 4;
    const PAYMENT_METHOD_BILL       = 5;

    const IS_UNPAID                 = 0;
    const IS_PAID                   = 1;

    const CURRENCY_DEFAULT          = 'EUR';

    const PROCEDURE_MANUAL  = 1;
    const PROCEDURE_AUTO    = 2;

    const STATUS_UNPAID     = -1;
    const STATUS_OPEN       = 0;
    const STATUS_CLOSED     = 1;

    const REFUND_PACKAGE    = 1;

    const FUNDS_REFUNDABLE          = 1;
    const FUNDS_NONREFUNDABLE       = 0;

    const BILLING_PREFIX            = 'PODCASTER-R';

    const BILL_EXTENSION            = '.pdf';
    const BILLS_STORAGE_DIR         = 'hostingstorage/bills';
    const BILLS_BACKUP_DIR          = 'backup/bills';

    protected $primaryKey = 'payment_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'payment_id' => 'int',
		'receiver_id' => 'int',
		'payer_id' => 'int',
		'usr_id' => 'int',
		'amount' => 'float',
		'amount_refunded' => 'float',
		'payment_method' => 'int',
		'is_refundable' => 'bool',
		'is_refunded' => 'bool',
        'state' => 'int',
	];

	protected $dates = [
		'date_created'
	];

	protected $fillable = [
		'payment_id',
		'receiver_id',
		'payer_id',
		'usr_id',
		'amount',
		'currency',
		'payment_method',
		'date_created',
		'bill_id',
		'is_refundable',
		'is_refunded',
		'amount_refundable',
		'is_paid',
		'comment',
		'state',
	];

    public static $paymentMethods   = [
        self::PAYMENT_METHOD_VOUCHER        => 'Voucher',
        self::PAYMENT_METHOD_LOCALBANK      => 'Local Bank',
        self::PAYMENT_METHOD_FOREIGNBANK    => 'Foreign Bank',
        self::PAYMENT_METHOD_PAYPAL         => 'PayPal',
        self::PAYMENT_METHOD_BILL           => 'Bill',
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::created(function(UserPayment $userPayment) {
            // This does not work here as long as bill is created through the legacy system
            if ($userPayment->is_refundable == self::FUNDS_REFUNDABLE) {
                if (isset($userPayment->payer->userbillingcontact)
                    && $userPayment->payer->userbillingcontact->email
                    && $userPayment->payer->userbillingcontact->bill_by_email) {
                    $userPayment->notify(new UserPaymentNotification);
                }
                LaravelMatomoTracker::queueEvent('payment', 'created', $userPayment->payment_method, $userPayment->amount);
                LaravelMatomoTracker::queueGoal(9, $userPayment->amount);
            }
        });
    }

	public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id', 'usr_id')->withTrashed();
    }

	public function payer()
    {
        return $this->belongsTo('App\Models\User', 'payer_id', 'usr_id')->withTrashed();
    }

    public function scopeOwner($query)
    {
        return $query->where('payer_id', '=', auth()->id())->where('payment_method', '>', 1);
    }

    public function scopeRefundable($query)
    {
        return $query->where('is_refundable', '=', 1);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', '=', self::IS_PAID);
    }

    public function setCurrency($value)
    {
        $this->attributes['currency'] = Str::uppercase($value);
    }

    public function getIdAttribute()
    {
        return $this->payment_id;
    }

    public function routeNotificationForMail()
    {
        return [$this->payer->userbillingcontact->email => $this->payer->userbillingcontact->first_name . ' ' .  $this->payer->userbillingcontact->last_name];
    }

    public function markAsPaid()
    {
        $this->update(['is_paid' => self::IS_PAID]);
    }
}
