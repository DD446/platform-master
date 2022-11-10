<?php

/**
 * Date: Thu, 25 Oct 2018 09:49:50 +0000.
 *
 * DONE: Track uploads user_uploads
 * DONE: Substract uploads from available space
 * DONE: Substract space when file is copied
 * DONE: Add space when upload is deleted in billing month
 * DONE: Add space when user bought space through extras
 * TODO: Add space when new accounting period started
 * TODO: Add space when user created his account
 * TODO: Change space when user switched package
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Space
 *
 * @property int $id
 * @property int $user_id
 * @property int $space
 * @property int $type
 * @property bool $is_available
 * @property bool $is_free
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class Space extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes, HasFactory;

	const TYPE_REGULAR = 1;
	const TYPE_EXTRA = 2;
	const TYPE_VOUCHER = 3;
	const TYPE_GIFT = 4;

	const AVAILABLE = 1;
	const UNAVAILABLE = 0;

	protected $casts = [
		'user_id' => 'int',
		'user_accounting_id' => 'int',
		'space' => 'int',
		'space_available' => 'int',
		'type' => 'int',
		'is_available' => 'bool',
		'is_free' => 'bool'
	];

	protected $fillable = [
		'user_id',
        'user_accounting_id',
		'space',
		'space_available',
		'type',
		'is_available',
		'is_free'
	];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::created(function (Space $space) {
            if ($space->type == Space::TYPE_REGULAR) {
                // Invalidate space of type REGULAR (from package) when new space of this type is booked
                Space::available()
                    ->whereUserId($space->user_id)
                    ->whereType(Space::TYPE_REGULAR)
                    ->where('id', '<', $space->id)
                    ->delete();
            }
        });

        static::deleted(function (Space $space) {
            $space->update(['is_available' => self::UNAVAILABLE]);
        });
    }

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class, 'user_id');
	}

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public function scopeAvailable()
    {
        return $this->where('is_available', '=', true);
    }

    public function scopeRegular()
    {
        return $this->where('type', '=', Space::TYPE_REGULAR);
    }

    public function scopeExtra()
    {
        return $this->where('type', '=', Space::TYPE_EXTRA);
    }

    public function getHumanFriendlySpaceAttribute()
    {
        return get_size_readable($this->space);
    }

    public function getHumanFriendlySpaceAvailableAttribute()
    {
        return get_size_readable($this->space_available);
    }

    public function getLocalizedDateAttribute()
    {
        // TODO: I18N
        return $this->created_at->formatLocalized('%d.%m.%Y');
    }
}
