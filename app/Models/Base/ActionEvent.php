<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActionEvent
 * 
 * @property int $id
 * @property string $batch_id
 * @property int $user_id
 * @property string $name
 * @property string $actionable_type
 * @property int $actionable_id
 * @property string $target_type
 * @property int $target_id
 * @property string $model_type
 * @property int|null $model_id
 * @property string $fields
 * @property string $status
 * @property string $exception
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $original
 * @property string|null $changes
 *
 * @package App\Models\Base
 */
class ActionEvent extends Model
{
	protected $table = 'action_events';

	protected $casts = [
		'user_id' => 'int',
		'actionable_id' => 'int',
		'target_id' => 'int',
		'model_id' => 'int'
	];
}
