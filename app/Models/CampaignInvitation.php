<?php

/**
 * Date: Tue, 13 Nov 2018 21:19:52 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CampaignInvitation
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property string $feed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\Campaign $campaign
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class CampaignInvitation extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'campaign_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'campaign_id',
		'user_id',
		'feed_id'
	];

	public function campaign()
	{
		return $this->belongsTo(\App\Models\Campaign::class);
	}

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class, 'user_id', 'usr_id');
	}
}
