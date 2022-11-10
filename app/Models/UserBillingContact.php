<?php

/**
 * Date: Mon, 24 Jun 2019 13:13:16 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\File;

/**
 * Class UserBillingContact
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $telephone
 * @property string $telefax
 * @property string $email
 * @property string $organisation
 * @property string $department
 * @property string $street
 * @property string $housenumber
 * @property string $city
 * @property string $country
 * @property string $post_code
 * @property string $vat_id
 * @property string $extras
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @package App\Models
 */
class UserBillingContact extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'user_id' => 'int',
		'bill_by_email' => 'bool',
	];

	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'telephone',
		'telefax',
		'email',
		'organisation',
		'department',
		'street',
		'housenumber',
		'city',
		'country',
		'post_code',
		'vat_id',
		'extras',
		'bill_by_email',
	];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        static::updated(function(UserBillingContact $userBillingContact) {
            $bills = UserPayment::where('payer_id', '=', $userBillingContact->user_id)->where('payment_method', '>', 1)->get();

            foreach($bills as $bill) {
                $file = storage_path(UserPayment::BILLS_STORAGE_DIR . get_user_path($userBillingContact->user->username) . DIRECTORY_SEPARATOR . $bill->bill_id . UserPayment::BILL_EXTENSION);

                if ($file && File::exists($file) && File::isFile($file)) {
                    File::delete($file);
                }
            }
        });

        parent::boot();
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'usr_id');
    }
}
