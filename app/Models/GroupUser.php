<?php

namespace App\Models;

use App\Models\Base\GroupUser as BaseGroupUser;

class GroupUser extends BaseGroupUser
{
	protected $fillable = [
		'user_id',
		'group_id'
	];
}
