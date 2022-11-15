<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserDpa
 * 
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $usr_id
 * @property int $av_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $organisation
 * @property string|null $register_court
 * @property string|null $register_number
 * @property string|null $representative
 * @property string|null $street
 * @property string|null $housenumber
 * @property string|null $post_code
 * @property string|null $city
 * @property string $country
 *
 * @package App\Models\Base
 */
class UserDpa extends Model
{
	use SoftDeletes;
	protected $table = 'user_dpas';

	protected $casts = [
		'usr_id' => 'int',
		'av_id' => 'int'
	];
}
