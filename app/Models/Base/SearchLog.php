<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SearchLog
 * 
 * @property int $id
 * @property string $query
 * @property bool $is_boolean_search
 * @property string|null $index_name
 * @property string|null $model
 * @property string|null $ids
 * @property int $hits
 * @property float $execution_time
 * @property string|null $driver
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SearchLog extends Model
{
	protected $table = 'search_logs';

	protected $casts = [
		'is_boolean_search' => 'bool',
		'hits' => 'int',
		'execution_time' => 'float'
	];
}
