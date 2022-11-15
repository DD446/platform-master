<?php

namespace App\Models;

use App\Models\Base\Usr as BaseUsr;

class Usr extends BaseUsr
{
	protected $hidden = [
		'password',
		'two_factor_secret',
		'remember_token'
	];

	protected $fillable = [
		'role_id',
		'username',
		'passwd',
		'password',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'remember_token',
		'first_name',
		'last_name',
		'name_title',
		'telephone',
		'telefax',
		'email',
		'url',
		'organisation',
		'department',
		'street',
		'housenumber',
		'feed_email',
		'city',
		'region',
		'country',
		'post_code',
		'gender',
		'is_acct_active',
		'is_trial',
		'date_created',
		'date_trialend',
		'created_by',
		'last_updated',
		'updated_by',
		'package_id',
		'terms_date',
		'terms_version',
		'privacy_date',
		'privacy_version',
		'funds',
		'forum_number_post',
		'register_court',
		'register_number',
		'vat_id',
		'board',
		'chairman',
		'representative',
		'mediarepresentative',
		'controlling_authority',
		'additional_specifications',
		'is_updating',
		'is_blocked',
		'is_protected',
		'parent_user_id',
		'is_supporter',
		'is_advertiser',
		'can_pay_by_bill',
		'has_paid',
		'super',
		'name',
		'avatar',
		'preferences',
		'last_login',
		'agency_enabled',
		'audiotakes_enabled',
		'new_package_id',
		'welcome_email_state',
		'available_stats_exports',
		'use_new_statistics'
	];
}
