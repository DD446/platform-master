<?php

namespace App\Models;

use App\Models\Base\ActionEvent as BaseActionEvent;

class ActionEvent extends BaseActionEvent
{
	protected $fillable = [
		'batch_id',
		'user_id',
		'name',
		'actionable_type',
		'actionable_id',
		'target_type',
		'target_id',
		'model_type',
		'model_id',
		'fields',
		'status',
		'exception',
		'original',
		'changes'
	];
}
