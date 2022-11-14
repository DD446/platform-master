<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
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
 * @property string $sort_order
 * @property string $sort_by
 * @property int|null $limit
 * @property int|null $offset
 * @property string|null $restrict
 * @property int|null $restrict_limit
 * @property string|null $filename
 * @property string $format
 * @property int $downloads
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
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
		'downloads' => 'int'
	];

	protected $dates = [
		'start',
		'end'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
