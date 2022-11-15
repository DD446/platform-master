<?php

namespace App\Models;

use App\Models\Base\WebhookCall as BaseWebhookCall;

class WebhookCall extends BaseWebhookCall
{
	protected $fillable = [
		'external_id',
		'name',
		'payload',
		'exception',
		'processed_at',
		'url',
		'headers'
	];
}
