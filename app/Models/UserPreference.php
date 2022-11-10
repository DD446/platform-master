<?php

/**
 * Date: Fri, 05 Apr 2019 11:24:06 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserPreference
 *
 * @property int $user_preference_id
 * @property int $usr_id
 * @property int $preference_id
 * @property string $value
 *
 * @package App\Models
 */
class UserPreference extends Eloquent
{
	protected $table = 'user_preference';
	protected $primaryKey = 'user_preference_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_preference_id' => 'int',
		'usr_id' => 'int',
		'preference_id' => 'int'
	];

	protected $fillable = [
		'usr_id',
		'preference_id',
		'value'
	];
}
