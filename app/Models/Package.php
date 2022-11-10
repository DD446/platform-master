<?php

/**
 * Date: Fri, 09 Mar 2018 09:05:09 +0000.
 */

namespace App\Models;

use App\Scopes\IsVisibleScope;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Package
 *
 * @property int $package_id
 * @property string $package_name
 * @property float $monthly_cost
 * @property int $paying_rhythm
 * @property bool $package_available
 * @property bool $is_hidden
 * @property bool $is_default
 * @property string $tld
 *
 * @package App\Models
 */
class Package extends Eloquent
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;

    const FEATURE_FEEDS = 'feeds';
    const FEATURE_FEEDS_EXTRA = 'feeds_extra';
    const FEATURE_STORAGE = 'storage';
    const FEATURE_STORAGE_EXTRA = 'storage_extra';
    const FEATURE_STATISTICS = 'statistics';
    const FEATURE_STATISTICS_EXPORT = 'statistics_export';
    const FEATURE_API = 'api';
    const FEATURE_BILL_ONLINE = 'bill_online';
    const FEATURE_BILL_PRINT = 'bill_print';
    const FEATURE_BLOGS = 'blogs';
    const FEATURE_DOMAINS = 'domains';
    const FEATURE_SUBDOMAINS = 'subdomains';
    const FEATURE_SUBDOMAINS_PREMIUM = 'subdomains_premium';
    const FEATURE_TRANSCODING = 'transcoding';
    const FEATURE_PROTECTION = 'protection';
    const FEATURE_SCHEDULER = 'scheduler';
    const FEATURE_AUPHONIC = 'auphonic';
    const FEATURE_BANDWIDTH = 'bandwidth';
    const FEATURE_MULTIUSER = 'multiuser';
    const FEATURE_SUPPORT = 'support';
    const FEATURE_PLAYER = 'player';
    const FEATURE_PLAYER_CONFIGURATION = 'player_configuration';
    const FEATURE_PLAYER_CUSTOMSTYLES = 'player_customstyles';
    const FEATURE_ADS = 'ads';
    const FEATURE_ADS_CUSTOM = 'ads_custom';
    const FEATURE_TEAMS = 'teams';
    const FEATURE_MEMBERS = 'members';
    const FEATURE_SOCIALMEDIA = 'socialmedia';

    const STATISTICS_SIMPLE         = 1;
    const STATISTICS_STANDARD       = 2;
    const STATISTICS_PRO            = 3;
    const STATISTICS_PRO_PLUS       = 4;
    const STATISTICS_PRO_CORP       = 5;

    const PAYMENT_PERIOD = 1;

    const CACHE_KEY_FEATURE_LIST = 'cache_key_package_feature_list';
    const CACHE_KEY_FEATURE_LIST_ALL = 'cache_key_package_feature_list_all';
    const CACHE_KEY_BENEFITS = 'cache_key_package_benefits';

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

	protected $fillable = [
		'package_name',
		'monthly_cost',
		'paying_rhythm',
		'package_available',
		'is_hidden',
		'is_default',
		'tld'
	];

    protected static function boot()
    {
        self::addGlobalScope(new IsVisibleScope());

        parent::boot();
    }

    public static function getFeatures()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        $aConstants = $oClass->getConstants();

        return array_filter($aConstants,
            function($v) {
                    return strpos($v, 'FEATURE_') === 0;
                },
            ARRAY_FILTER_USE_KEY);
    }

    public static function getStatisticTypes()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        $aConstants = $oClass->getConstants();

        return array_filter($aConstants,
            function($v) {
                    return strpos($v, 'STATISTICS_') === 0;
                },
            ARRAY_FILTER_USE_KEY);
    }

    public function mappings()
    {
        return $this->hasMany(
            'App\Models\PackageFeatureMapping',
            'package_id',
            'package_id')
            ->with(['feature']);
    }

    /**
     * Used for Laravel Nova releation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function package_feature_mapping()
    {
        return $this->mappings();
    }

    public function features()
    {
        return $this->hasManyThrough(
            'App\Models\PackageFeature',
            'App\Models\PackageFeatureMapping',
            'package_id',
            'package_feature_id',
            'package_id',
            'package_feature_id');
    }

    /**
     * Used for Laravel Nova releation
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function package_feature()
    {
        return $this->features();
    }
}
