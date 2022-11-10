<?php

namespace App\Models;

use App\Models\Base\AudiotakesPayoutContact as BaseAudiotakesPayoutContact;

class AudiotakesPayoutContact extends BaseAudiotakesPayoutContact
{
	protected $fillable = [
		'user_id',
		'name',
		'tax_id',
		'paypal',
		'bank_account_owner',
		'iban',
		'country',
		'vat_id',
		'audiotakes_contract_id',
		'is_verified',
	];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
