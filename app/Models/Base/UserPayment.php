<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property Carbon $date_created
 * @property string $bill_id
 * @property int $is_refundable
 * @property bool $is_refunded
 * @property float|null $amount_refunded
 * @property string|null $comment
 * @property bool $is_paid
 * @property int $state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class UserPayment extends Model
{
	use SoftDeletes;
	protected $table = 'user_payments';
	protected $primaryKey = 'payment_id';
	public $incrementing = false;

	protected $casts = [
		'payment_id' => 'int',
		'receiver_id' => 'int',
		'payer_id' => 'int',
		'usr_id' => 'int',
		'amount' => 'float',
		'payment_method' => 'int',
		'is_refundable' => 'int',
		'is_refunded' => 'bool',
		'amount_refunded' => 'float',
		'is_paid' => 'bool',
		'state' => 'int'
	];

	protected $dates = [
		'date_created'
	];
}
