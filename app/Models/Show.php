<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Events\ShowAddEvent;

class Show extends Model
{
    const PUBLISH_PAST      = -1;
    const PUBLISH_DRAFT     = 0;
    const PUBLISH_NOW       = 1;
    const PUBLISH_FUTURE    = 2;

    protected $connection = 'mongodb';
    protected $primaryKey = 'guid';

    protected static function boot()
    {
        static::saved(function(Show $show) {
            event(new ShowAddEvent($show->feed->username, $show->feed->feed_id, $show['guid']));
        });

/*        static::updated(function(Show $show) {
            event(new ShowAddEvent(auth()->user()->username, \request('feedId'), \request('guid')));
        });

        static::deleted(function (Show $show) {
        });*/

        parent::boot();
    }

    protected $fillable = [
        'itunes',
        'googleplay',
        'podcast',
        'feed_id',
        'guid',
        'title',
        'author',
        'copyright',
        'is_public',
        'description',
        'publishing_date',
        'publishing_time',
        'link',
        'show_media',
    ];

    protected $casts = [
        'itunes' => Itunes::class,
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
