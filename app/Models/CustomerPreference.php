<?php

namespace App\Models;

use App\Models\Base\CustomerPreference as BaseCustomerPreference;

class CustomerPreference extends BaseCustomerPreference
{
	protected $fillable = [
		'image_name',
		'user_id',
		'landing_page_id',
		'company_name'
	];
}
