<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
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
 * @property Usr $usr
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

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
