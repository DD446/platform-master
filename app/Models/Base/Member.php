<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Team;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Member
 * 
 * @property int $id
 * @property int $user_id
 * @property int $team_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Team $team
 * @property Usr $usr
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

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
