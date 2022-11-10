<?php

/**
 * Date: Tue, 28 Aug 2018 07:38:54 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Page
 *
 * @property int $id
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Page extends Eloquent implements HasMedia
{
    use InteractsWithMedia;

	protected $fillable = [
		'title'
	];

    /**
     * Register the conversions that should be performed.
     *
     * @return array
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('grey')
            ->greyscale()

            ->withResponsiveImages();
    }
}
