<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyAnalytic
 * 
 * @property int $id
 * @property int $user_id
 * @property string $feed_id
 * @property string|null $show_title
 * @property string $file
 * @property Carbon $date
 * @property string $version
 * @property array $data
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class SpotifyAnalytic extends Model
{
	protected $table = 'spotify_analytics';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'data' => 'json'
	];

	protected $dates = [
		'date'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
