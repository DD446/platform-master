<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserBillingContact
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $telephone
 * @property string|null $telefax
 * @property string|null $email
 * @property bool $bill_by_email
 * @property string|null $organisation
 * @property string|null $department
 * @property string|null $street
 * @property string|null $housenumber
 * @property string|null $city
 * @property string|null $country
 * @property string|null $post_code
 * @property string|null $vat_id
 * @property string|null $extras
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class UserBillingContact extends Model
{
	use SoftDeletes;
	protected $table = 'user_billing_contacts';

	protected $casts = [
		'user_id' => 'int',
		'bill_by_email' => 'bool'
	];
}
