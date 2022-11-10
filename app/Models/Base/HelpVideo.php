<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HelpVideo
 * 
 * @property int $id
 * @property string $page_title
 * @property string $page_description
 * @property string $title
 * @property string|null $subtitle
 * @property string $content
 * @property string $username
 * @property int|null $poster
 * @property int|null $mp4
 * @property int|null $webm
 * @property int|null $ogv
 * @property bool $is_public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class HelpVideo extends Model
{
	use SoftDeletes;
	protected $table = 'help_videos';

	protected $casts = [
		'poster' => 'int',
		'mp4' => 'int',
		'webm' => 'int',
		'ogv' => 'int',
		'is_public' => 'bool'
	];
}
