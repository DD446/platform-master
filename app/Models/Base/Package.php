<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Package
 * 
 * @property int $package_id
 * @property string $package_name
 * @property float|null $monthly_cost
 * @property int $paying_rhythm
 * @property bool $package_available
 * @property bool $is_hidden
 * @property bool $is_default
 * @property string $tld
 *
 * @package App\Models\Base
 */
class Package extends Model
{
	protected $table = 'package';
	protected $primaryKey = 'package_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'package_id' => 'int',
		'monthly_cost' => 'float',
		'paying_rhythm' => 'int',
		'package_available' => 'bool',
		'is_hidden' => 'bool',
		'is_default' => 'bool'
	];
}
