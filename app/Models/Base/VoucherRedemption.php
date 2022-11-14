<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VoucherRedemption
 * 
 * @property int $id
 * @property int $user_id
 * @property int $voucher_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Usr $usr
 * @property Voucher $voucher
 *
 * @package App\Models\Base
 */
class VoucherRedemption extends Model
{
	protected $table = 'voucher_redemptions';

	protected $casts = [
		'user_id' => 'int',
		'voucher_id' => 'int'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}

	public function voucher()
	{
		return $this->belongsTo(Voucher::class);
	}
}
