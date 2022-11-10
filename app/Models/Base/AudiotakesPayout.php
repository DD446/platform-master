<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContract;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesPayout
 *
 * @property int $id
 * @property int $user_id
 * @property int $audiotakes_contract_id
 * @property float $funds
 * @property float $funds_open
 * @property float $funds_raw
 * @property float $holdback
 * @property float $share
 * @property string $currency
 * @property int $month
 * @property int $year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property AudiotakesContract $audiotakes_contract
 * @property User $user
 *
 * @package App\Models\Base
 */
class AudiotakesPayout extends Model
{
	use SoftDeletes;

	protected $table = 'audiotakes_payouts';

	protected $casts = [
		'user_id' => 'int',
		'audiotakes_contract_id' => 'int',
		'funds' => 'float',
		'funds_open' => 'float',
		'funds_raw' => 'float',
		'holdback' => 'float',
		'share' => 'float',
		'month' => 'int',
		'year' => 'int'
	];

	public function audiotakes_contract()
	{
		return $this->belongsTo(AudiotakesContract::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
