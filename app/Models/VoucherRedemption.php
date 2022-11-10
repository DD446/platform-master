<?php

namespace App\Models;

use App\Models\Base\VoucherRedemption as BaseVoucherRedemption;

class VoucherRedemption extends BaseVoucherRedemption
{
	protected $fillable = [
		'user_id',
		'voucher_id',
		'amount'
	];

    protected static function booted()
    {
        parent::booted();

        static::created(function(VoucherRedemption $voucherRedemption) {
            $voucher = $voucherRedemption->voucher;
            if ($voucher->amount > 0) {
                $voucher->decrement('amount', 1);
            }
        });
    }
}
