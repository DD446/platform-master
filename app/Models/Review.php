<?php

/**
 * Date: Thu, 05 Jul 2018 09:57:34 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Review
 *
 * @property int $id
 * @property string $q1
 * @property string $q2
 * @property string $q3
 * @property string $q4
 * @property string $q5
 * @property bool $is_public
 * @property int $usr_id
 * @property bool $is_published
 * @property string $published_cite
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Review extends Eloquent
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;

    const REVIEW_CACHE_KEY_LIST_WITH_LOGO = 'reviews_with_logo';

	protected $casts = [
		'is_public' => 'bool',
		'usr_id' => 'int',
		'is_published' => 'bool'
	];

	protected $fillable = [
		'q1',
		'q2',
		'q3',
		'q4',
		'q5',
		'is_public',
		'usr_id',
		'is_published',
		'published_cite'
	];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'usr_id', 'usr_id')->withDefault();
    }

    public function scopePublic()
    {
        return $this->where('is_public', '=', 1);
    }

    public function scopePublished()
    {
        return $this->where('is_published', '=', 1);
    }

    public function scopeOwner($query)
    {
        return $query->where('usr_id', '=', auth()->id());
    }

    public function getUserIdAttribute()
    {
        return $this->usr_id;
    }
}
