<?php

namespace App\Models;

use App\Models\Base\RoleUser as BaseRoleUser;

class RoleUser extends BaseRoleUser
{
	protected $fillable = [
		'user_id',
		'role_id'
	];
}
