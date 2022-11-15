<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ShowTemplate
 * 
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $feed_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $author
 * @property string|null $copyright
 * @property string|null $link
 * @property string|null $itunes_title
 * @property string|null $itunes_subtitle
 * @property string|null $itunes_summary
 * @property string $itunes_episode_type
 * @property int|null $itunes_season
 * @property bool $itunes_explicit
 * @property int|null $itunes_logo
 * @property int $is_public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class ShowTemplate extends Model
{
	use SoftDeletes;
	protected $table = 'show_templates';

	protected $casts = [
		'user_id' => 'int',
		'itunes_season' => 'int',
		'itunes_explicit' => 'bool',
		'itunes_logo' => 'int',
		'is_public' => 'int'
	];
}
