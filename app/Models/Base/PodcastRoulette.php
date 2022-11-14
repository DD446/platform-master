<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\PodcastRouletteMatch;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
 * @property bool $first_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 * @property Collection|PodcastRouletteMatch[] $podcast_roulette_matches
 *
 * @package App\Models\Base
 */
class PodcastRoulette extends Model
{
	use SoftDeletes;
	protected $table = 'podcast_roulettes';

	protected $casts = [
		'user_id' => 'int',
		'version' => 'int',
		'first_time' => 'bool'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}

	public function podcast_roulette_matches()
	{
		return $this->hasMany(PodcastRouletteMatch::class, 'roulette_partner_id');
	}
}
