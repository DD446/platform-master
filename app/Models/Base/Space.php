<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\UserAccounting;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Space
 * 
 * @property int $id
 * @property int $user_id
 * @property int $user_accounting_id
 * @property int $space
 * @property int $space_available
 * @property int $type
 * @property bool $is_available
 * @property bool $is_free
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property UserAccounting $user_accounting
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class Space extends Model
{
	use SoftDeletes;
	protected $table = 'spaces';

	protected $casts = [
		'user_id' => 'int',
		'user_accounting_id' => 'int',
		'space' => 'int',
		'space_available' => 'int',
		'type' => 'int',
		'is_available' => 'bool',
		'is_free' => 'bool'
	];

	public function user_accounting()
	{
		return $this->belongsTo(UserAccounting::class);
	}

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
