<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserForbidden
 * 
 * @property int $id
 * @property string $username
 *
 * @package App\Models\Base
 */
class UserForbidden extends Model
{
	protected $table = 'user_forbidden';
	public $timestamps = false;
}
