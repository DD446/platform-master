<?php

namespace App\Models;

use App\Models\Base\NovaTrixAttachment as BaseNovaTrixAttachment;

class NovaTrixAttachment extends BaseNovaTrixAttachment
{
	protected $fillable = [
		'attachable_type',
		'attachable_id',
		'attachment',
		'disk',
		'url'
	];
}
