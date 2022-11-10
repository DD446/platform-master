<?php

namespace App\Models;

use App\Jobs\CalculateIabMinSize;
use App\Models\Base\UserUpload as BaseUserUpload;

class UserUpload extends BaseUserUpload
{
	protected $fillable = [
		'user_id',
		'file_id',
		'file_size',
		'iab_min_size',
		'file_name',
        'space_id',
        'space_used',
	];

    protected static function boot()
    {
        static::created(function(UserUpload $userUpload) {
            CalculateIabMinSize::dispatch($userUpload);
        });

        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'usr_id');
    }

    public function space()
    {
        return $this->belongsTo(\App\Models\Space::class);
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function getHumanFriendlySizeAttribute()
    {
        return get_size_readable($this->file_size);
    }

    public function getHumanFriendlySpaceUsedAttribute()
    {
        return get_size_readable($this->space_used);
    }
}
