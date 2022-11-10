<?php

/**
 * Date: Fri, 09 Mar 2018 09:07:28 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\ContactUsMail;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class ContactUs
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $enquiry_type
 * @property string $user_comment
 *
 * @package App\Models
 */
class ContactUs extends Eloquent implements HasMedia
{
    use Notifiable, InteractsWithMedia, SoftDeletes, SerializesModels;

	protected $table = 'contact_us';

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'name',
		'email',
		'enquiry_type',
		'comment'
	];

    protected static function boot()
    {
        // auto-sets values on creation
        static::created(function(ContactUs $contactUs) {
            $contactUs->sendNotification();
        });

        parent::boot();
    }

    public function sendNotification()
    {
        $this->notify(new ContactUsMail());
    }

    public function routeNotificationForMail()
    {
        return [config('mail.contactus') => config('app.name') . ' Team'];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email')->withDefault();
    }
}
