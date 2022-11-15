<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Change
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property bool $is_public
 * @property int $likes
 * @property int $dislikes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class Change extends Model
{
	use SoftDeletes;
	protected $table = 'changes';

	protected $casts = [
		'is_public' => 'bool',
		'likes' => 'int',
		'dislikes' => 'int'
	];
}
