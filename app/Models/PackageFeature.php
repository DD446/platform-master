<?php
/**
 * Date: Fri, 09 Mar 2018 09:06:13 +0000.
 */

namespace App\Models;

//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class PackageFeature
 *
 * @property int $package_feature_id
 * @property string $feature_name
 *
 * @package App\Models
 */
class PackageFeature extends Eloquent
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;

	protected $table = 'package_feature';
	protected $primaryKey = 'package_feature_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'package_feature_id' => 'int'
	];

	protected $fillable = [
		'feature_name'
	];

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
            $pfs = PackageFeatureSeq::create();
            $model->package_feature_id = $pfs->id;
        });
    }

    public function package_feature_mapping()
    {
        return $this->belongsToMany(PackageFeatureMapping::class);
    }
}
