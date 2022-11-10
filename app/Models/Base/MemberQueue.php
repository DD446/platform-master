<?php

/**
 *
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\User;

/**
 * Class MemberQueue
 *
 * @property int $id
 * @property string $email
 * @property string $hash
 * @property int $team_id
 * @property int $owner_id
 * @property int $role_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 *
 * @property User $user
 * @property Team $team
 *
 * @package App\Models\Base
 */
class MemberQueue extends Model
{
	protected $table = 'member_queue';

	protected $casts = [
		'team_id' => 'int',
		'owner_id' => 'int',
		'role_id' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'owner_id');
	}

	public function team()
	{
		return $this->belongsTo(Team::class);
	}
}
