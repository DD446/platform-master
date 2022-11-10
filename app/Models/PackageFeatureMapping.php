<?php

/**
 * Date: Fri, 09 Mar 2018 09:06:16 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class PackageFeatureMapping
 *
 * @property int $package_feature_mapping_id
 * @property int $package_feature_id
 * @property int $package_id
 * @property int $units
 * @property int $status
 *
 * @package App\Models
 */
class PackageFeatureMapping extends Eloquent
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;

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

	protected $fillable = [
		'package_feature_id',
		'package_id',
		'units',
		'status'
	];

    protected $with = ['package_feature'];

    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $pfms = PackageFeatureMappingSeq::create();
            $model->package_feature_mapping_id = $pfms->id;
        });
    }

	public function feature()
    {
        return $this->belongsTo('App\Models\PackageFeature', 'package_feature_id', 'package_feature_id');
    }

    /**
     * Used for Laravel Nova relations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function package_feature()
    {
        return $this->feature();
    }

    public function package()
    {
        return $this->belongsTo('App\Models\Package', 'package_id', 'package_id');
    }
}
