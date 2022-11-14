<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesBankTransfer;
use App\Models\AudiotakesContract;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesContractPartner
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $organisation
 * @property string|null $street
 * @property string|null $housenumber
 * @property string|null $post_code
 * @property string|null $city
 * @property string|null $country
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $vat_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 * @property Collection|AudiotakesBankTransfer[] $audiotakes_bank_transfers
 * @property Collection|AudiotakesContract[] $audiotakes_contracts
 *
 * @package App\Models\Base
 */
class AudiotakesContractPartner extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_contract_partners';

	protected $casts = [
		'user_id' => 'int'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}

	public function audiotakes_bank_transfers()
	{
		return $this->hasMany(AudiotakesBankTransfer::class);
	}

	public function audiotakes_contracts()
	{
		return $this->hasMany(AudiotakesContract::class);
	}
}
