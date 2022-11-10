<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserVoucherRedemption
 *
 * @property int $id
 * @property int $user_id
 * @property int $voucher_id
 * @property int $amount
 *
 * @property User $user
 * @property Voucher $voucher
 *
 * @package App\Models\Base
 */
class VoucherRedemption extends Model
{
	protected $casts = [
		'user_id' => 'int',
		'voucher_id' => 'int',
		'amount' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}

	public function voucher()
	{
		return $this->belongsTo(Voucher::class);
	}

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
