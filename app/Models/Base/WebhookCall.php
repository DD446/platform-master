<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WebhookCall
 * 
 * @property int $id
 * @property string|null $external_id
 * @property string $name
 * @property array|null $payload
 * @property string|null $exception
 * @property Carbon|null $processed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $url
 * @property array|null $headers
 *
 * @package App\Models\Base
 */
class WebhookCall extends Model
{
	protected $table = 'webhook_calls';

	protected $casts = [
		'payload' => 'json',
		'headers' => 'json'
	];

	protected $dates = [
		'processed_at'
	];
}
