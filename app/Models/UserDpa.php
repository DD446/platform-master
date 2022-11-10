<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDpa extends Model
{
    use SoftDeletes;

    protected $attributes = [
    ];

    protected $fillable = [
        'av_id',
        'first_name',
        'last_name',
        'organisation',
        'register_court',
        'register_number',
        'representative',
        'street',
        'housenumber',
        'post_code',
        'city',
        'country',
    ];

    public function isOwner()
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->usr_id == auth()->id();
    }

    public function attributes()
    {
        return $this->hasMany('App\Models\UserDpaAttribute');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_id', 'usr_id');
    }
}
