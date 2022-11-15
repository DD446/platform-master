<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserDpaAttribute
 * 
 * @property int $id
 * @property int $user_dpa_id
 * @property string $data
 * @property int $type
 *
 * @package App\Models\Base
 */
class UserDpaAttribute extends Model
{
	protected $table = 'user_dpa_attributes';
	public $timestamps = false;

	protected $casts = [
		'user_dpa_id' => 'int',
		'type' => 'int'
	];
}
