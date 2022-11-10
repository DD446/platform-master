<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesContractPartner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudiotakesContract
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $telephone
 * @property string|null $organisation
 * @property string $street
 * @property string $housenumber
 * @property string $post_code
 * @property string $city
 * @property string $country
 * @property string|null $vat_id
 * @property Carbon $audiotakes_date_accepted
 * @property Carbon|null $audiotakes_date_canceled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $usr
 *
 * @package App\Models\Base
 */
class AudiotakesContract extends Model
{
	protected $table = 'audiotakes_contracts';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $dates = [
		'audiotakes_date_accepted',
		'audiotakes_date_canceled'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

    public function audiotakes_contract_partner()
    {
        return $this->belongsTo(AudiotakesContractPartner::class);
    }

    public function partner()
    {
        return $this->audiotakes_contract_partner();
    }
}
