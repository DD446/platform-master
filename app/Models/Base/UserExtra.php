<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserExtra
 * 
 * @property int $extras_id
 * @property int $usr_id
 * @property int $extras_type
 * @property int $extras_count
 * @property string $extras_description
 * @property Carbon $date_created
 * @property Carbon $date_start
 * @property Carbon $date_end
 * @property int $is_repeating
 *
 * @package App\Models\Base
 */
class UserExtra extends Model
{
	protected $table = 'user_extras';
	protected $primaryKey = 'extras_id';
	public $timestamps = false;

	protected $casts = [
		'usr_id' => 'int',
		'extras_type' => 'int',
		'extras_count' => 'int',
		'is_repeating' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_start',
		'date_end'
	];
}
