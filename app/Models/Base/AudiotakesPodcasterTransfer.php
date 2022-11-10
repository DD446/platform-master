<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudiotakesPodcasterTransfer
 *
 * @property int $id
 * @property int $user_id
 * @property float $funds
 * @property bool $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class AudiotakesPodcasterTransfer extends Model
{
	protected $table = 'audiotakes_podcaster_transfers';

	protected $casts = [
		'user_id' => 'int',
		'funds' => 'float',
		'is_paid' => 'bool'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
