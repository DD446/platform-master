<?php

/**
 * Date: Tue, 13 Nov 2018 21:01:50 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Supporter
 *
 * @property int $id
 * @property int $user_id
 * @property int $supporter_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class Supporter extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'user_id' => 'int',
		'supporter_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'supporter_id'
	];

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class, 'user_id')->withDefault();
	}

	public function supporter()
	{
		return $this->belongsTo(\App\Models\User::class, 'supporter_id')->withDefault();
	}

    /**
     * Local scope to only select regular users
     *
     * @return mixed
     */
    public function scopePersonal($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    /**
     * Local scope to only select regular users
     *
     * @return mixed
     */
    public function scopeSupported($query)
    {
        return $query->where('supporter_id', '=', auth()->id());
    }

    /**
     * Local scope to only select regular users
     *
     * @return mixed
     */
    public function scopeOwner($query)
    {
        return $query->where('supporter_id', '=', auth()->id());
    }
}
