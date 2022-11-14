<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\CustomerPreference;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LandingPage
 * 
 * @property int $id
 * @property string $page_title
 * @property string $page_description
 * @property string $title
 * @property string|null $subtitle
 * @property string|null $teaser
 * @property string $content
 * @property bool $is_public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|CustomerPreference[] $customer_preferences
 *
 * @package App\Models\Base
 */
class LandingPage extends Model
{
	use SoftDeletes;
	protected $table = 'landing_pages';

	protected $casts = [
		'is_public' => 'bool'
	];

	public function customer_preferences()
	{
		return $this->hasMany(CustomerPreference::class);
	}
}
