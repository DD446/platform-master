<?php

namespace App\Models;

use App\Models\Base\AudiotakesId as BaseAudiotakesId;

class AudiotakesId extends BaseAudiotakesId
{
	protected $fillable = [
		'identifier',
		'user_id',
		'feed_id',
		'audiotakes_contract_id'
	];
}
