<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Models\Base\UserEmailQueue as BaseUserEmailQueue;
use App\Notifications\UserEmailChangeNotification;

class UserEmailQueue extends BaseUserEmailQueue
{
    use Notifiable;

	protected $fillable = [
		'user_id',
		'email',
		'hash',
		'date_created'
	];

    protected static function boot()
    {
        // auto-sets values on creation
        static::created(function(UserEmailQueue $userEmailQueue) {
            $userEmailQueue->notify(new UserEmailChangeNotification());
        });

        parent::boot();
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
