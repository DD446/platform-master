<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Member;
use App\Models\MemberQueue;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Team
 * 
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 * @property Collection|MemberQueue[] $member_queues
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

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'owner_id');
	}

	public function member_queues()
	{
		return $this->hasMany(MemberQueue::class);
	}

	public function members()
	{
		return $this->hasMany(Member::class);
	}
}
