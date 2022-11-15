<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\VoucherRedemption;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Voucher
 * 
 * @property int $id
 * @property int|null $voucher_action_id
 * @property int $usage_count
 * @property Carbon|null $valid_until
 * @property string $voucher_code
 * @property string|null $comment
 * @property int|null $valid_for
 * @property int $amount
 * @property int $amount_per_person
 * @property Carbon $date_created
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|VoucherRedemption[] $voucher_redemptions
 *
 * @package App\Models\Base
 */
class Voucher extends Model
{
	use SoftDeletes;
	protected $table = 'vouchers';

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

	public function voucher_redemptions()
	{
		return $this->hasMany(VoucherRedemption::class);
	}
}
