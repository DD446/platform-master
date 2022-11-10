<?php

/**
 * Date: Thu, 24 Jan 2019 22:37:39 +0000.
 */

namespace App\Models;

use App\Scopes\UserScope;
////use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class SpotifyAnalytic
 *
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon $date
 * @property string $version
 * @property array $data
 *
 * @package App\Models
 */
class SpotifyAnalytic extends Model
{
    protected $connection = 'mongodb';

	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		//'data' => 'json'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'user_id',
		'feed_id',
		'show_title',
		'file',
		'date',
		'version',
		'data'
	];

    protected static function boot()
    {
        self::addGlobalScope(new UserScope);

        parent::boot();
    }
}
