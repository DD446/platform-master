<?php

namespace App\Models;

use App\Models\Base\PackageChange as BasePackageChange;

class PackageChange extends BasePackageChange
{
    const TYPE_UPGRADE = 1;
    const TYPE_DOWNGRADE = 2;
    const TYPE_DELETE = 3;

	protected $fillable = [
		'user_id',
		'type',
		'from',
		'to'
	];

    public function scopeUpgrade($query)
    {
        return $query->where('type', '=', self::TYPE_UPGRADE);
    }

    public function scopeDowngrade($query)
    {
        return $query->where('type', '=', self::TYPE_DOWNGRADE);
    }

    public function scopeDeletion($query)
    {
        return $query->where('type', '=', self::TYPE_DELETE);
    }
}
