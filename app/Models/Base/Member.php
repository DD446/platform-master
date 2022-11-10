<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Team;
use App\Models\User;
use App\Scopes\IsActiveScope;

/**
 * Class Member
 *
 * @property int $id
 * @property int $user_id
 * @property int $team_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 *
 * @property Team $team
 * @property User $user
 *
 * @package App\Models\Base
 */
class Member extends Model
{
	use SoftDeletes;

	protected $table = 'members';

	protected $casts = [
		'user_id' => 'int',
		'team_id' => 'int'
	];

	public function team()
	{
		return $this->belongsTo(Team::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(IsActiveScope::class);
	}
}
