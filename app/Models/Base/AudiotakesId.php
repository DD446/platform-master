<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContract;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesId
 * 
 * @property int $id
 * @property string $identifier
 * @property int|null $user_id
 * @property string|null $feed_id
 * @property int|null $audiotakes_contract_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property AudiotakesContract|null $audiotakes_contract
 * @property Usr|null $usr
 *
 * @package App\Models\Base
 */
class AudiotakesId extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_ids';

	protected $casts = [
		'user_id' => 'int',
		'audiotakes_contract_id' => 'int'
	];

	public function audiotakes_contract()
	{
		return $this->belongsTo(AudiotakesContract::class);
	}

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
