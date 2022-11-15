<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\CampaignInvitation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Campaign
 * 
 * @property int $id
 * @property int $advertiser_id
 * @property string $title
 * @property string $description
 * @property string $name
 * @property string $reply_to
 * @property string $itunes_category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|CampaignInvitation[] $campaign_invitations
 *
 * @package App\Models\Base
 */
class Campaign extends Model
{
	use SoftDeletes;
	protected $table = 'campaigns';

	protected $casts = [
		'advertiser_id' => 'int'
	];

	public function campaign_invitations()
	{
		return $this->hasMany(CampaignInvitation::class);
	}
}
