<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\LandingPage;
use App\Models\User;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerPreference
 * 
 * @property int $id
 * @property string $image_name
 * @property int $user_id
 * @property int $landing_page_id
 * @property string $company_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property LandingPage $landing_page
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class CustomerPreference extends Model
{
	protected $table = 'customer_preferences';

	protected $casts = [
		'user_id' => 'int',
		'landing_page_id' => 'int'
	];

	public function landing_page()
	{
		return $this->belongsTo(LandingPage::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id','usr_id');
	}
}
