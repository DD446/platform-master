<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PodcastRoulette
 *
 * @property int $id
 * @property int $user_id
 * @property string $feed_id
 * @property string $email
 * @property string $podcasters
 * @property int $version
 * @property int $first_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class PodcastRoulette extends Model
{
	use SoftDeletes;

	protected $table = 'podcast_roulettes';

    protected $casts = [
		'user_id' => 'int',
        'first_time' => 'bool',
		'version' => 'int',
	];

    protected $fillable = [
        'feed_id',
        'email',
        'podcasters',
        'first_time',
    ];

    public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
