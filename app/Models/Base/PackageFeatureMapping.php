<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PackageFeatureMapping
 * 
 * @property int $package_feature_mapping_id
 * @property int $package_feature_id
 * @property int $package_id
 * @property int|null $units
 * @property int $status
 *
 * @package App\Models\Base
 */
class PackageFeatureMapping extends Model
{
	protected $table = 'package_feature_mapping';
	protected $primaryKey = 'package_feature_mapping_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'package_feature_mapping_id' => 'int',
		'package_feature_id' => 'int',
		'package_id' => 'int',
		'units' => 'int',
		'status' => 'int'
	];
}
