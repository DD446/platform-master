<?php

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Member;
use App\Models\MemberQueue;
use App\Models\User;

/**
 * Class Team
 *
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 *
 * @property User $user
 * @property Collection|Member[] $members
 *
 * @package App\Models\Base
 */
class Team extends Model
{
	use SoftDeletes;

	protected $table = 'teams';

	protected $casts = [
		'owner_id' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'owner_id');
	}

	public function members()
	{
		return $this->hasMany(Member::class, 'team_id', 'id');
	}

	public function queuedMembers()
	{
		return $this->hasMany(MemberQueue::class);
	}
}
