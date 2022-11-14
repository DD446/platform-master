<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesPayout;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesPayoutLog
 * 
 * @property int $id
 * @property int $audiotakes_payout_id
 * @property float $funds
 * @property float $funds_open
 * @property float $funds_raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property AudiotakesPayout $audiotakes_payout
 *
 * @package App\Models\Base
 */
class AudiotakesPayoutLog extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_payout_logs';

	protected $casts = [
		'audiotakes_payout_id' => 'int',
		'funds' => 'float',
		'funds_open' => 'float',
		'funds_raw' => 'float'
	];

	public function audiotakes_payout()
	{
		return $this->belongsTo(AudiotakesPayout::class);
	}
}
