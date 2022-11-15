<?php

namespace App\Models;

use App\Models\Base\NovaPendingTrixAttachment as BaseNovaPendingTrixAttachment;

class NovaPendingTrixAttachment extends BaseNovaPendingTrixAttachment
{
	protected $fillable = [
		'draft_id',
		'attachment',
		'disk'
	];
}
