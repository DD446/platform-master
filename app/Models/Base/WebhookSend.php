<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WebhookSend
 * 
 * @property int $id
 * @property int $user_id
 * @property string $service
 * @property array $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class WebhookSend extends Model
{
	protected $table = 'webhook_sends';

	protected $casts = [
		'user_id' => 'int',
		'payload' => 'json'
	];
}
