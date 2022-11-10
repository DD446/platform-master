<?php

/**
 * Date: Fri, 27 Jul 2018 12:45:42 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Notifications\UserActivationNotification;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserQueue
 *
 * @property int $user_queue_id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $organisation
 * @property string $password
 * @property string $passwd
 * @property string $hash
 * @property string $source
 * @property string $country
 * @property bool $newsletter
 * @property \Carbon\Carbon $date_created
 * @property bool $package_id
 *
 * @package App\Models
 */
class UserQueue extends Model
{
    use SoftDeletes, Notifiable;

    const UPDATED_AT = null;

	protected $table = 'user_queue';
	protected $primaryKey = 'user_queue_id';

	protected $casts = [
		'user_queue_id' => 'int',
		'package_id' => 'int'
	];

	protected $dates = [
		'created_at',
		'deleted_at',
	];

	protected $hidden = [
		'hash'
	];

	protected $fillable = [
		'username',
		'email',
		'hash',
		'source',
		'package_id',
		'country',
	];


    public function sendActivationNotification()
    {
        $this->notify(new UserActivationNotification());
    }
}
