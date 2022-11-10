<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PodcastRouletteMatch
 *
 * @property int $id
 * @property int $podcaster_one
 * @property int $podcaster_two
 * @property int|null $file_id
 * @property int|null $cover_id
 * @property int $version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class PodcastRouletteMatch extends Model
{
	protected $table = 'podcast_roulette_matches';

	protected $casts = [
		'roulette_id' => 'int',
		'roulette_partner_id' => 'int',
		'file_id' => 'int',
		'cover_id' => 'int',
	];

	public function player()
	{
		return $this->belongsTo(\App\Models\PodcastRoulette::class, 'roulette_id', 'id');
	}

	public function partner()
	{
		return $this->belongsTo(\App\Models\PodcastRoulette::class, 'roulette_partner_id', 'id');
	}
}
