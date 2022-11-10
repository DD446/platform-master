<?php

/**
 * Date: Fri, 09 Mar 2018 09:07:48 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Faqcategory
 *
 * @property int $faqcategory_id
 * @property string $title
 * @property string $perms
 * @property int $trans_id
 * @property int $parent_id
 * @property int $root_id
 * @property int $left_id
 * @property int $right_id
 * @property int $order_id
 * @property int $level_id
 *
 * @package App\Models
 */
class Faqcategory extends Eloquent
{
	protected $table = 'faqcategory';
	protected $primaryKey = 'faqcategory_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'faqcategory_id' => 'int',
		'trans_id' => 'int',
		'parent_id' => 'int',
		'root_id' => 'int',
		'left_id' => 'int',
		'right_id' => 'int',
		'order_id' => 'int',
		'level_id' => 'int'
	];

	protected $fillable = [
		'title',
		'perms',
		'trans_id',
		'parent_id',
		'root_id',
		'left_id',
		'right_id',
		'order_id',
		'level_id'
	];
}
