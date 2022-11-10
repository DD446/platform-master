<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StatsExport
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $start
 * @property Carbon|null $end
 * @property string|null $feed_id
 * @property string|null $show_id
 * @property string $order
 * @property string $sort
 * @property string|null $restrict
 * @property int|null $limit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class StatsExport extends Model
{
	use SoftDeletes;
	protected $table = 'stats_exports';

	protected $casts = [
		'user_id' => 'int',
		'limit' => 'int',
		'offset' => 'int',
		'restrict_limit' => 'int',
		'downloads' => 'int',
	];

	protected $dates = [
		'start',
		'end'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
