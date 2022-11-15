<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlayerConfig
 * 
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $player_type
 * @property string $title
 * @property string $default_album_art
 * @property int $delay_between_audio
 * @property float $initial_playback_speed
 * @property bool $hide_playlist_in_singlemode
 * @property bool $show_playlist
 * @property bool $show_info
 * @property bool $enable_shuffle
 * @property string $preload
 * @property bool $debug_player
 * @property string $player_configurable_id
 * @property string $player_configurable_type
 * @property string|null $feed_id
 * @property bool $sharing
 * @property string|null $text_color
 * @property string|null $background_color
 * @property string|null $icon_color
 * @property string|null $icon_fg_color
 * @property string|null $progressbar_color
 * @property string|null $progressbar_buffer_color
 * @property string|null $custom_styles
 * @property string $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class PlayerConfig extends Model
{
	use SoftDeletes;
	protected $table = 'player_configs';

	protected $casts = [
		'uuid' => 'binary',
		'user_id' => 'int',
		'player_type' => 'int',
		'delay_between_audio' => 'int',
		'initial_playback_speed' => 'float',
		'hide_playlist_in_singlemode' => 'bool',
		'show_playlist' => 'bool',
		'show_info' => 'bool',
		'enable_shuffle' => 'bool',
		'debug_player' => 'bool',
		'sharing' => 'bool'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
