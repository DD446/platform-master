<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PackageFeature
 * 
 * @property int $package_feature_id
 * @property string $feature_name
 *
 * @package App\Models\Base
 */
class PackageFeature extends Model
{
	protected $table = 'package_feature';
	protected $primaryKey = 'package_feature_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'package_feature_id' => 'int'
	];
}
