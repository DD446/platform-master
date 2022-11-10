<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Models\Base\MemberQueue as BaseMemberQueue;
use App\Notifications\MemberQueueNotification;

class MemberQueue extends BaseMemberQueue
{
    use Notifiable;

	protected $fillable = [
		'email',
		'hash',
		'team_id',
		'role_id'
	];

    protected static function boot()
    {
        // auto-sets values on creation
        static::created(function(MemberQueue $memberQueue) {
            $memberQueue->sendNotification();
        });

        static::updated(function(MemberQueue $memberQueue) {
            $memberQueue->sendNotification();
        });

        parent::boot();
    }

    public function getCreatedAttribute($date)
    {
        // TODO: I18N
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y h:i');
    }

    public function sendNotification()
    {
        $this->notify(new MemberQueueNotification());
    }

    public function owner()
    {
        return $this->hasOneThrough(User::class, Team::class, 'id', 'usr_id', 'team_id', 'owner_id');
    }
}
