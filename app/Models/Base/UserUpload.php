<?php

/**
 *
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserUpload
 *
 * @property int $id
 * @property string $file_id
 * @property int $file_size
 * @property string $file_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Base
 */
class UserUpload extends Model
{
	use SoftDeletes;

	protected $table = 'user_uploads';

	protected $casts = [
		'user_id' => 'int',
		'file_size' => 'int',
        'space_id' => 'int',
        'space_used' => 'int',
	];
}
