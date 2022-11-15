<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 * 
 * @property int $id
 * @property string|null $q1
 * @property string|null $q2
 * @property string|null $q3
 * @property string|null $q4
 * @property string|null $q5
 * @property bool $is_public
 * @property int $usr_id
 * @property bool $is_published
 * @property string $published_cite
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Review extends Model
{
	protected $table = 'reviews';

	protected $casts = [
		'is_public' => 'bool',
		'usr_id' => 'int',
		'is_published' => 'bool'
	];
}
