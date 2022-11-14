<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContractPartner;
use App\Models\AudiotakesPayoutContact;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudiotakesBankTransfer
 * 
 * @property int $id
 * @property int $user_id
 * @property int $audiotakes_payout_contact_id
 * @property int|null $audiotakes_contract_partner_id
 * @property float $funds
 * @property bool $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property AudiotakesContractPartner|null $audiotakes_contract_partner
 * @property AudiotakesPayoutContact $audiotakes_payout_contact
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class AudiotakesBankTransfer extends Model
{
	protected $table = 'audiotakes_bank_transfers';

	protected $casts = [
		'user_id' => 'int',
		'audiotakes_payout_contact_id' => 'int',
		'audiotakes_contract_partner_id' => 'int',
		'funds' => 'float',
		'is_paid' => 'bool'
	];

	public function audiotakes_contract_partner()
	{
		return $this->belongsTo(AudiotakesContractPartner::class);
	}

	public function audiotakes_payout_contact()
	{
		return $this->belongsTo(AudiotakesPayoutContact::class);
	}

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
