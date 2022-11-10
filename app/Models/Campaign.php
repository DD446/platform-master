<?php

/**
 * Date: Tue, 13 Nov 2018 21:20:14 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Campaign
 *
 * @property int $id
 * @property int $advertiser_id
 * @property string $title
 * @property string $description
 * @property string $reply_to
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \Illuminate\Database\Eloquent\Collection $invitations
 *
 * @package App\Models
 */
class Campaign extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'advertiser_id' => 'int',
		'itunes_category' => 'string',
	];

	protected $fillable = [
		'advertiser_id',
		'title',
		'description',
		'name',
		'reply_to',
		'itunes_category',
	];

	public function campaigninvitations()
	{
		return $this->hasMany(\App\Models\CampaignInvitation::class);
	}

    public function scopeOwner($query)
    {
        return $query->where('advertiser_id', '=', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'advertiser_id', 'usr_id');
    }
}
