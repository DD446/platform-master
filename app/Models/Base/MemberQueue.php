<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MemberQueue
 * 
 * @property int $id
 * @property string $email
 * @property string $hash
 * @property int $team_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Team $team
 *
 * @package App\Models\Base
 */
class MemberQueue extends Model
{
	protected $table = 'member_queue';

	protected $casts = [
		'team_id' => 'int',
		'role_id' => 'int'
	];

	public function team()
	{
		return $this->belongsTo(Team::class);
	}
}
