<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PackageChange
 *
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property int|null $from
 * @property int|null $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class PackageChange extends Model
{
	protected $table = 'package_changes';

	protected $casts = [
		'user_id' => 'int',
		'type' => 'int',
		'from' => 'int',
		'to' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
