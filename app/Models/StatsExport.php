<?php

namespace App\Models;

use App\Events\StatsExportedEvent;
use App\Events\StatsExportEvent;
use App\Models\Base\StatsExport as BaseStatsExport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StatsExport extends BaseStatsExport
{
    const LATEST_COUNT = 5;

	protected $fillable = [
		'user_id',
		'start',
		'end',
		'feed_id',
		'show_id',
		'sort_order',
		'sort_by',
		'restrict',
		'restrict_limit',
		'offset',
		'limit',
		'downloads',
		'filename',
	];

    protected static function boot()
    {
        static::created(function(StatsExport $export) {
            Log::debug("STATS EXPORT created");
            event(new StatsExportEvent($export));
        });

        static::saved(function(StatsExport $export) {
            // Important! Other the calling job ends up in a loop
            if ($export->filename) {
                $user = User::find($export->user_id);
                $availableExports = get_package_feature_statistics_export($user->package, $user);

                if ($availableExports['used'] > $availableExports['included']) {
                    $user->decrement('available_stats_exports');
                }
            }
        });

        static::updated(function(StatsExport $export) {
            Log::debug("STATS EXPORT updated");
            event(new StatsExportedEvent($export));
        });

        static::deleted(function(StatsExport $export) {
            Log::debug("STATS EXPORT deleted");
            File::delete(public_path($export->filename . '.' . $export->format));
        });

        parent::boot();
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
