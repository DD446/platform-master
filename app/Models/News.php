<?php

namespace App\Models;

use App\Scopes\IsPublicScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class News extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia, \Spiritix\LadaCache\Database\LadaCacheTrait;


    protected $table = 'news';

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'title',
        'teaser',
        'body',
        'likes',
        'dislikes',
        'is_public',
        'is_sticky',
        'author',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new IsPublicScope);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(254);
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
