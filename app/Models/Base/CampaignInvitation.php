<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Campaign;
use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CampaignInvitation
 * 
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property string $feed_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Campaign $campaign
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class CampaignInvitation extends Model
{
	use SoftDeletes;
	protected $table = 'campaign_invitations';

	protected $casts = [
		'campaign_id' => 'int',
		'user_id' => 'int'
	];

	public function campaign()
	{
		return $this->belongsTo(Campaign::class);
	}

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
