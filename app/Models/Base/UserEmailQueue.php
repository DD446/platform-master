<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserEmailQueue
 * 
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $hash
 * @property Carbon $date_created
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class UserEmailQueue extends Model
{
	protected $table = 'user_email_queue';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $dates = [
		'date_created'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
