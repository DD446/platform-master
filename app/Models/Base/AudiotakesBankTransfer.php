<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContractPartner;
use App\Models\AudiotakesPayoutContact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudiotakesBankTransfer
 *
 * @property int $id
 * @property int $user_id
 * @property float $funds
 * @property bool $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models\Base
 */
class AudiotakesBankTransfer extends Model
{
	protected $table = 'audiotakes_bank_transfers';

	protected $casts = [
		'user_id' => 'int',
		'funds' => 'float',
		'is_paid' => 'bool'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}

    public function audiotakes_payout_contact()
    {
        return $this->belongsTo(AudiotakesPayoutContact::class);
    }

    public function audiotakes_contract_partner()
    {
        return $this->belongsTo(AudiotakesContractPartner::class);
    }
}
