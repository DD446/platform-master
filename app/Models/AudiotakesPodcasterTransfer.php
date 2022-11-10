<?php

namespace App\Models;

use App\Models\Base\AudiotakesPodcasterTransfer as BaseAudiotakesPodcasterTransfer;

class AudiotakesPodcasterTransfer extends BaseAudiotakesPodcasterTransfer
{
    const UNPAID = 0;
    const PAID = 1;

	protected $fillable = [
		'user_id',
		'funds',
		'is_paid'
	];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function markAsPaid()
    {
        $this->update(['is_paid' => self::PAID]);
    }
}
