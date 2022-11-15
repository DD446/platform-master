<?php

namespace App\Models;

use App\Models\Base\Country as BaseCountry;

class Country extends BaseCountry
{
	protected $fillable = [
		'capital',
		'citizenship',
		'country_code',
		'currency',
		'currency_code',
		'currency_sub_unit',
		'currency_symbol',
		'currency_decimals',
		'full_name',
		'iso_3166_2',
		'iso_3166_3',
		'name',
		'region_code',
		'sub_region_code',
		'eea',
		'calling_code',
		'flag'
	];
}
