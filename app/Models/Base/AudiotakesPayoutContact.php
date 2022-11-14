<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesBankTransfer;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesPayoutContact
 * 
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $tax_id
 * @property string|null $paypal
 * @property string|null $iban
 * @property string|null $bank_account_owner
 * @property string $country
 * @property string|null $vat_id
 * @property bool $is_verified
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 * @property Collection|AudiotakesBankTransfer[] $audiotakes_bank_transfers
 *
 * @package App\Models\Base
 */
class AudiotakesPayoutContact extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_payout_contacts';

	protected $casts = [
		'user_id' => 'int',
		'is_verified' => 'bool'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}

	public function audiotakes_bank_transfers()
	{
		return $this->hasMany(AudiotakesBankTransfer::class);
	}
}
