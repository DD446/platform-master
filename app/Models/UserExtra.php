<?php

/**
 * Date: Mon, 27 Aug 2018 10:18:43 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserExtra
 *
 * @property int $extras_id
 * @property int $usr_id
 * @property int $extras_type
 * @property int $extras_count
 * @property string $extras_description
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $date_start
 * @property \Carbon\Carbon $date_end
 * @property bool $is_repeating
 *
 * @package App\Models
 */
class UserExtra extends Eloquent
{
    use HasFactory;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'date_created';

    const IS_ONETIME        = 0;
    const IS_REPEATING      = 1;

    const EXTRA_FEED        = 1;
    const EXTRA_STORAGE     = 2;
    const EXTRA_PLAYERCONFIGURATION = 3;
    const EXTRA_MEMBER      = 4;
    const EXTRA_STATSEXPORT = 5;

    const DEFAULT_FEED      = 1;
    const DEFAULT_STORAGE   = 52428800; # 50MB

	protected $primaryKey = 'extras_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'extras_id' => 'int',
		'usr_id' => 'int',
		'extras_type' => 'int',
		'extras_count' => 'int',
		'is_repeating' => 'bool'
	];

	protected $dates = [
		'date_created',
		'date_start',
		'date_end'
	];

	protected $fillable = [
		'usr_id',
		'extras_type',
		'extras_count',
		'extras_description',
		'date_created',
		'date_start',
		'date_end',
		'is_repeating'
	];

	protected static $types = [
	    'feed' => self::EXTRA_FEED,
	    'storage' => self::EXTRA_STORAGE,
	    'playerconfiguration' => self::EXTRA_PLAYERCONFIGURATION,
	    'member' => self::EXTRA_MEMBER,
	    'statsexport' => self::EXTRA_STATSEXPORT,
    ];

	protected static $pieces = [
        self::EXTRA_FEED => 1,
        self::EXTRA_STORAGE => 50,
        self::EXTRA_PLAYERCONFIGURATION => 10,
        self::EXTRA_MEMBER => 1,
        self::EXTRA_STATSEXPORT => 5,
    ];

	protected static $bookable = [
        self::EXTRA_FEED => [self::IS_REPEATING],
        self::EXTRA_STORAGE => [self::IS_ONETIME, self::IS_REPEATING],
        self::EXTRA_PLAYERCONFIGURATION => [self::IS_REPEATING],
        self::EXTRA_MEMBER => [self::IS_REPEATING],
        self::EXTRA_STATSEXPORT => [self::IS_ONETIME, self::IS_REPEATING],
    ];


    protected static function boot()
    {
        static::saved(function(UserExtra $extra) {
            if ($extra->extras_type === UserExtra::EXTRA_STATSEXPORT) {
                $user = User::find($extra->user_id);

                if ($user) {
                    $user->increment('available_stats_exports', $extra->extras_count * self::$pieces[UserExtra::EXTRA_STATSEXPORT]);
                }
            }
        });

        parent::boot();
    }

    public function scopeOwner($query)
    {
        return $query->where('usr_id', '=', auth()->id());
    }

    public function scopeFeed($query)
    {
        return $query->where('extras_type', '=', self::EXTRA_FEED);
    }

    public function scopeStorage($query)
    {
        return $query->where('extras_type', '=', self::EXTRA_STORAGE);
    }

    public function scopePlayerconfiguration($query)
    {
        return $query->where('extras_type', '=', self::EXTRA_PLAYERCONFIGURATION);
    }

    public function scopeMember($query)
    {
        return $query->where('extras_type', '=', self::EXTRA_MEMBER);
    }

    public static function getTypes()
    {
        return self::$types;
    }

    public static function getPieces()
    {
        return self::$pieces;
    }

    public static function getBookable()
    {
        return self::$bookable;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_id', 'usr_id');
    }

    public function getUserIdAttribute()
    {
        return $this->usr_id;
    }
}
