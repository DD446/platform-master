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
 * Class Feedback
 * 
 * @property int $id
 * @property int $user_id
 * @property string $comment
 * @property int $type
 * @property string|null $entity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Usr $usr
 *
 * @package App\Models\Base
 */
class Feedback extends Model
{
	use SoftDeletes;
	protected $table = 'feedbacks';

	protected $casts = [
		'user_id' => 'int',
		'type' => 'int'
	];

	public function usr()
	{
		return $this->belongsTo(Usr::class, 'user_id');
	}
}
