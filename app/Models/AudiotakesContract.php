<?php

namespace App\Models;

use App\Models\Base\AudiotakesContract as BaseAudiotakesContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class AudiotakesContract extends BaseAudiotakesContract
{
    use SoftDeletes, HybridRelations, HasFactory;

    const PLATFORM_HOLDBACK = 10;

    const GENERAL_SUBSTRACTION = 35; // preshare

    const DEFAULT_SHARE = 60; // was 70 up until 26.01.2022

    const DEFAULT_CURRENCY = 'EUR';

    const MINIMUM_PAYOUT_VALUE = 5;

    protected $connection = 'mysql';

    //protected $with = ['feed'];

	protected $fillable = [
		'user_id',
		'feed_id',
		'identifier',
		'audiotakes_date_accepted',
		'audiotakes_date_canceled',
		'created_at',
		'deleted_at',
		'share',
        'audiotakes_contract_partner_id',
    ];

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }

    public static function getFreeIdentifier()
    {
        $identifier = AudiotakesContract::whereNull('user_id')->whereNull('feed_id')->value('identifier');

        // No free identifier was found - create one
        if (!$identifier) {
            $identifier = self::saveNewIdentifier();
            Log::error("ERROR: audiotakes: No free identifier was found. Created new one: {$identifier}.");
        }

        return $identifier;
    }

    public static function saveNewIdentifier()
    {
        $identifier = self::createRandomIdentifier();

        while(AudiotakesContract::whereIdentifier($identifier)->count() > 0) {
            $identifier = self::createRandomIdentifier();
        }

        AudiotakesContract::create([
            'identifier' => $identifier
        ]);

        return $identifier;
    }

    public static function createRandomIdentifier()
    {
        return Str::lower('at-' . Str::random(5));
    }

    public function scopeSigned($query)
    {
        return $query->whereNotNull('user_id')->whereNotNull('feed_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usr_id')->withTrashed();
    }

    public function feed()
    {
        return $this->belongsTo(Feed::where('username', '=', $this->user->username), 'feed_id', 'feed_id');
    }
}
