<?php

namespace App\Models;

use App\Models\Base\AudiotakesPayout as BaseAudiotakesPayout;

class AudiotakesPayout extends BaseAudiotakesPayout
{
	protected $fillable = [
		'user_id',
		'audiotakes_contract_id',
		'funds',
		'funds_open',
		'funds_raw',
		'holdback',
		'share',
		'currency',
		'month',
		'year'
	];

    protected static function boot()
    {
        static::created(function(AudiotakesPayout $audiotakesPayout) {
            $data = $audiotakesPayout->only(['funds', 'funds_open', 'funds_raw']);
            $data['audiotakes_payout_id'] = $audiotakesPayout->id;
            AudiotakesPayoutLog::create($data);
        });

        static::updated(function(AudiotakesPayout $audiotakesPayout) {
            $data = $audiotakesPayout->only(['funds', 'funds_open', 'funds_raw']);
            $data['audiotakes_payout_id'] = $audiotakesPayout->id;
            AudiotakesPayoutLog::create($data);
        });

        parent::boot();
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
