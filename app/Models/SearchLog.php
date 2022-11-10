<?php

/**
 *
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SearchLog
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $query
 * @property bool $is_boolean_search
 * @property string $index_name
 * @property string $model
 * @property string $ids
 * @property int $hits
 * @property float $execution_time
 * @property string $driver
 *
 * @package App\Models
 */
class SearchLog extends Model
{
	protected $table = 'search_logs';

	protected $casts = [
		'is_boolean_search' => 'bool',
		'hits' => 'int',
		'execution_time' => 'float',
		'ids' => 'array',
	];

	protected $fillable = [
		'query',
		'is_boolean_search',
		'index_name',
		'model',
		'ids',
		'hits',
		'execution_time',
		'driver'
	];
}
