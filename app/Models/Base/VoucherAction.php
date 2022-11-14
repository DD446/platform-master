<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class VoucherAction
 * 
 * @property int $id
 * @property string $name
 * @property int $type
 * @property int $units
 * @property int $usage_limit
 * @property int|null $reuse_period
 * @property int|null $reuse_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class VoucherAction extends Model
{
	use SoftDeletes;
	protected $table = 'voucher_actions';

	protected $casts = [
		'type' => 'int',
		'units' => 'int',
		'usage_limit' => 'int',
		'reuse_period' => 'int',
		'reuse_type' => 'int'
	];
}
