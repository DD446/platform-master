<?php

namespace App\Models;

use App\Models\Base\VoucherAction as BaseVoucherAction;

class VoucherAction extends BaseVoucherAction
{
    const TYPE_EXTENDED_TRIALPERIOD = 1;
    const TYPE_EXTRA_STORAGE = 2;

	protected $fillable = [
		'name',
		'type',
		'units',
		'usage_limit',
		'reuse_period',
		'reuse_type'
	];

    protected static $typeUnitMapping = [
        self::TYPE_EXTENDED_TRIALPERIOD => 'days',
    ];

    public static function getTypeUnitMapping()
    {
        return static::$typeUnitMapping;
    }

    public static function getUnitByType($unit)
    {
        return self::getTypeUnitMapping()[$unit];
    }

    public function getUnitLabel()
    {
        return trans_choice('accounting.voucher_action_unit', $this->type);
    }
}
