<?php

namespace App\Models;

use App\Models\Base\AudiotakesContractPartner as BaseAudiotakesContractPartner;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudiotakesContractPartner extends BaseAudiotakesContractPartner
{
    use SoftDeletes;

	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'organisation',
		'street',
		'housenumber',
		'post_code',
		'city',
		'country',
		'email',
		'telephone',
		'vat_id'
	];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
