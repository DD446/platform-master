<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserQueue
 * 
 * @property int $user_queue_id
 * @property string $username
 * @property string $email
 * @property string $hash
 * @property string|null $source
 * @property int $package_id
 * @property string $country
 * @property Carbon|null $created_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class UserQueue extends Model
{
	use SoftDeletes;
	protected $table = 'user_queue';
	protected $primaryKey = 'user_queue_id';
	public $timestamps = false;

	protected $casts = [
		'package_id' => 'int'
	];
}
