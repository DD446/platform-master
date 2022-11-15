<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GroupUser
 * 
 * @property int $id
 * @property int $user_id
 * @property string $group_id
 *
 * @package App\Models\Base
 */
class GroupUser extends Model
{
	protected $table = 'group_user';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];
}
