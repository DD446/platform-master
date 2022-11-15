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
 * Class SpotifyAnalyticsExport
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $show_title
 * @property string $data_type
 * @property Carbon $start
 * @property Carbon $end
 * @property bool $is_exported
 * @property int $download_counter
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class SpotifyAnalyticsExport extends Model
{
	use SoftDeletes;
	protected $table = 'spotify_analytics_export';

	protected $casts = [
		'user_id' => 'int',
		'is_exported' => 'bool',
		'download_counter' => 'int'
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
