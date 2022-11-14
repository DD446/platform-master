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
 * Class UserUpload
 * 
 * @property int $id
 * @property int $user_id
 * @property string $file_id
 * @property int $file_size
 * @property int|null $iab_min_size
 * @property string $file_name
 * @property int $space_id
 * @property int $space_used
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
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
		'iab_min_size' => 'int',
		'space_id' => 'int',
		'space_used' => 'int'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
