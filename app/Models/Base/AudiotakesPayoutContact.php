<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AudiotakesPayoutContact
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $tax_id
 * @property string|null $paypal
 * @property string|null $iban
 * @property string $country
 * @property string|null $vat_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
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

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'usr_id');
	}
}
