<?php

namespace App\Models;

use App\Models\Base\AudiotakesPayoutLog as BaseAudiotakesPayoutLog;

class AudiotakesPayoutLog extends BaseAudiotakesPayoutLog
{
	protected $fillable = [
		'audiotakes_payout_id',
		'funds',
		'funds_open',
		'funds_raw',
	];
}
