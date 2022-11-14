<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\PodcastRoulette;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PodcastRouletteMatch
 * 
 * @property int $id
 * @property int $roulette_id
 * @property int $roulette_partner_id
 * @property int|null $file_id
 * @property int|null $cover_id
 * @property string|null $shownotes
 * @property int $version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PodcastRoulette $podcast_roulette
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
		'version' => 'int'
	];

	public function podcast_roulette()
	{
		return $this->belongsTo(PodcastRoulette::class, 'roulette_partner_id');
	}
}
