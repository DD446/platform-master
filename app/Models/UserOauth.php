<?php

/**
 * Date: Sun, 21 Jul 2019 18:24:45 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserOauth
 *
 * @property int $id
 * @property int $user_id
 * @property string $screen_name
 * @property string $oauth_token
 * @property string $service
 *
 * @package App\Models
 */
class UserOauth extends Eloquent
{
    const SERVICE_FACEBOOK      = 'facebook';
    const SERVICE_TWITTER       = 'twitter';
    const SERVICE_YOUTUBE       = 'youtube';
    const SERVICE_VIMEO         = 'vimeo';
    const SERVICE_AUPHONIC      = 'auphonic';
    const SERVICE_PODCASTER     = 'podcaster';
    const SERVICE_PODCAST       = 'podcast';

	protected $table = 'user_oauth';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $hidden = [
		'oauth_token',
		'user_id',
	];

	protected $fillable = [
		'user_id',
		'screen_name',
		'oauth_token',
		'service'
	];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function scopeService($query, $service)
    {
        return $query->where('service', '=', $service);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usr_id');
    }

    public function getTokenAttribute()
    {
        $oToken = unserialize($this->oauth_token);

        return $oToken->getToken();
    }
}
