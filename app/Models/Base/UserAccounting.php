<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Space;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAccounting
 * 
 * @property int $accounting_id
 * @property int $usr_id
 * @property int $activity_type
 * @property int $activity_characteristic
 * @property string $activity_description
 * @property float $amount
 * @property string $currency
 * @property Carbon $date_created
 * @property Carbon $date_start
 * @property Carbon $date_end
 * @property bool $procedure
 * @property bool $status
 * 
 * @property Collection|Space[] $spaces
 *
 * @package App\Models\Base
 */
class UserAccounting extends Model
{
	protected $table = 'user_accounting';
	protected $primaryKey = 'accounting_id';
	public $timestamps = false;

	protected $casts = [
		'usr_id' => 'int',
		'activity_type' => 'int',
		'activity_characteristic' => 'int',
		'amount' => 'float',
		'procedure' => 'bool',
		'status' => 'bool'
	];

	protected $dates = [
		'date_created',
		'date_start',
		'date_end'
	];

	public function spaces()
	{
		return $this->hasMany(Space::class);
	}
}
