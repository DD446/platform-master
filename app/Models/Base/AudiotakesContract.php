<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContractPartner;
use App\Models\AudiotakesId;
use App\Models\AudiotakesPayout;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesContract
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $audiotakes_contract_partner_id
 * @property string|null $feed_id
 * @property string $identifier
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $organisation
 * @property string|null $street
 * @property string|null $housenumber
 * @property string|null $post_code
 * @property string|null $city
 * @property string|null $country
 * @property string|null $vat_id
 * @property float $share
 * @property Carbon|null $audiotakes_date_accepted
 * @property Carbon|null $audiotakes_date_canceled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property AudiotakesContractPartner|null $audiotakes_contract_partner
 * @property Collection|AudiotakesId[] $audiotakes_ids
 * @property Collection|AudiotakesPayout[] $audiotakes_payouts
 *
 * @package App\Models\Base
 */
class AudiotakesContract extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_contracts';

	protected $casts = [
		'user_id' => 'int',
		'audiotakes_contract_partner_id' => 'int',
		'share' => 'float'
	];

	protected $dates = [
		'audiotakes_date_accepted',
		'audiotakes_date_canceled'
	];

	public function audiotakes_contract_partner()
	{
		return $this->belongsTo(AudiotakesContractPartner::class);
	}

	public function audiotakes_ids()
	{
		return $this->hasMany(AudiotakesId::class);
	}

	public function audiotakes_payouts()
	{
		return $this->hasMany(AudiotakesPayout::class);
	}
}
