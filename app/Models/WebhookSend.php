<?php

namespace App\Models;

use App\Models\Base\WebhookSend as BaseWebhookSend;

class WebhookSend extends BaseWebhookSend
{
	protected $fillable = [
		'user_id',
		'service',
		'status',
		'payload'
	];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usr_id');
    }
}
