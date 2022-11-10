<?php

/**
 * Date: Tue, 26 Feb 2019 15:52:34 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserForbidden
 *
 * @property int $id
 * @property string $username
 *
 * @package App\Models
 */
class UserForbidden extends Eloquent
{
	protected $table = 'user_forbidden';
	public $timestamps = false;

	protected $fillable = [
		'username'
	];
}
