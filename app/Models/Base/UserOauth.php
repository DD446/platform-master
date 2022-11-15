<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserOauth
 * 
 * @property int $id
 * @property int $user_id
 * @property string $screen_name
 * @property string|null $oauth_token
 * @property string $service
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class UserOauth extends Model
{
	protected $table = 'user_oauth';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
