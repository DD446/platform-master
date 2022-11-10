<?php

/**
 *
 */

namespace App\Models\Base;

use App\Models\AudiotakesBankTransfer;
use App\Models\AudiotakesContract;
use App\Models\User;
use Carbon\Carbon;
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
 * @property string|null $telephone
 * @property string|null $vat_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class AudiotakesContractPartner extends Model
{
	use SoftDeletes;
	protected $table = 'audiotakes_contract_partners';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usr_id');
    }

    public function audiotakes_contract()
    {
        return $this->belongsToMany(AudiotakesContract::class);
    }

    public function audiotakes_bank_transfer()
    {
        return $this->hasMany(AudiotakesBankTransfer::class);
    }
}
