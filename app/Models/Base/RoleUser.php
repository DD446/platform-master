<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleUser
 * 
 * @property int $id
 * @property int $user_id
 * @property string $role_id
 *
 * @package App\Models\Base
 */
class RoleUser extends Model
{
	protected $table = 'role_user';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];
}
