<?php

/**
 * Date: Tue, 12 Feb 2019 11:59:20 +0000.
 */

namespace App\Models;

use Illuminate\Support\Facades\File;
use App\Jobs\ExportSpotifyStatistics;
use App\Scopes\UserScope;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class SpotifyAnalyticsExport
 *
 * @property int $id
 * @property int $user_id
 * @property string $show_title
 * @property \Carbon\Carbon $start
 * @property \Carbon\Carbon $end
 * @property bool $is_exported
 * @property int $download_counter
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\User $usr
 *
 * @package App\Models
 */
class SpotifyAnalyticsExport extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	const DEFAULT_EXTENSION = '.zip';

	protected $table = 'spotify_analytics_export';

	protected $casts = [
		'user_id' => 'int',
		'is_exported' => 'bool',
		'download_counter' => 'int'
	];

	protected $dates = [
		'start',
		'end'
	];

	protected $fillable = [
		'user_id',
		'show_title',
		'start',
		'end',
		'is_exported',
		'download_counter'
	];

    protected static function boot()
    {
        self::addGlobalScope(new UserScope);

        static::created(function(SpotifyAnalyticsExport $spotifyAnalyticsExport) {
            ExportSpotifyStatistics::dispatch($spotifyAnalyticsExport);
        });

        static::deleted(function(SpotifyAnalyticsExport $spotifyAnalyticsExport) {
            File::delete(self::getPath($spotifyAnalyticsExport) .  $spotifyAnalyticsExport->id . self::DEFAULT_EXTENSION);
        });

        parent::boot();
    }

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class, 'user_id');
	}

	public function scopeExported()
    {
        return $this->where('is_exported', '=', true);
    }

    public static function getPath(SpotifyAnalyticsExport $spotifyAnalyticsExport)
    {
        return storage_path('app/exports/spotify/stats/' . $spotifyAnalyticsExport->user_id . '/');
    }
}
