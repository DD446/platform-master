<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class News
 * 
 * @property int $id
 * @property string $title
 * @property string $teaser
 * @property string $body
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_public
 * @property bool $is_sticky
 * @property int $likes
 * @property int $dislikes
 * @property string $author
 *
 * @package App\Models\Base
 */
class News extends Model
{
	protected $table = 'news';

	protected $casts = [
		'is_public' => 'bool',
		'is_sticky' => 'bool',
		'likes' => 'int',
		'dislikes' => 'int'
	];
}
