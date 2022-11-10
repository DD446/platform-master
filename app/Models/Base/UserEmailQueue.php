<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserEmailQueue
 *
 * @property int $usr_id
 * @property string $email
 * @property string $hash
 * @property Carbon $date_created
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
}
