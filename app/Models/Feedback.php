<?php

/**
 * Date: Tue, 13 Nov 2018 21:01:44 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Notifications\Notifiable;
use App\Mail\FeedbackMail;
use App\Notifications\FeedbackNotification;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Feedback
 *
 * @property int $id
 * @property int $user_id
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class Feedback extends Eloquent implements HasMedia
{
	use \Illuminate\Database\Eloquent\SoftDeletes, InteractsWithMedia, Notifiable;

	protected static $types = [
	    1 => 'faq_dislike',
	    2 => 'feature_mediathek',
	    3 => 'feature_podcasts',
	    4 => 'feature_statecheck',
	    5 => 'feature_submit',
	    6 => 'feature_player',
	    7 => 'feature_changes',
	    8 => 'feature_teams',
	    9 => 'feature_addshow',
	    10 => 'feature_news',
	    11 => 'user_delete',
	    12 => 'feature_share',
	    13 => 'feature_statistics',
    ];

	protected $table = 'feedbacks';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'comment',
		'type',
		'entity',
	];

    protected static function boot()
    {
        // auto-sets values on creation
        static::created(function(Feedback $feedback) {
            $feedback->sendNotification();
        });

        parent::boot();
    }

    public static function types()
    {
        return self::$types;
    }

    public function user()
	{
		return $this->belongsTo(\App\Models\User::class, 'user_id');
	}

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);
    }

    public function sendNotification()
    {
        $this->notify(new FeedbackNotification());
    }

    public function routeNotificationForMail()
    {
        return [config('mail.contactus') => config('app.name') . ' Team'];
    }
}
