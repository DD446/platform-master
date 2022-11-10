<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Change extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes, \Spiritix\LadaCache\Database\LadaCacheTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'changes';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'is_public', 'likes', 'dislikes', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['is_public', 'created_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'bool',
        'likes' => 'int',
        'dislikes' => 'int',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['published'];

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getPublishedAttribute()
    {
        return Carbon::createFromTimeString($this->attributes['created_at'])->diffForHumans();
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);

        $this->addMediaConversion('detail')
            ->width(300)
            ->height(300);
    }
}
