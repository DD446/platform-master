<?php

namespace App\Models;

use App\Models\Base\ContactU as BaseContactU;

class ContactU extends BaseContactU
{
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'enquiry_type',
		'comment',
		'name'
	];
}
