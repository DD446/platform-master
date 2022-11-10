<?php

namespace App\Models;

use App\Models\Base\Member as BaseMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends BaseMember
{
    use HasFactory;

	protected $fillable = [
		'user_id',
		'team_id'
	];

	public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usr_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
