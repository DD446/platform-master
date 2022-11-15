<?php

namespace App\Models;

use App\Models\Base\AdminPasswordReset as BaseAdminPasswordReset;

class AdminPasswordReset extends BaseAdminPasswordReset
{
	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'email',
		'token'
	];
}
