<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AudiotakesBankTransfer;
use App\Models\AudiotakesContractPartner;
use App\Models\AudiotakesId;
use App\Models\AudiotakesPayout;
use App\Models\AudiotakesPayoutContact;
use App\Models\AudiotakesPodcasterTransfer;
use App\Models\CampaignInvitation;
use App\Models\CustomerPreference;
use App\Models\Feedback;
use App\Models\Member;
use App\Models\PackageChange;
use App\Models\PlayerConfig;
use App\Models\PodcastRoulette;
use App\Models\Space;
use App\Models\SpotifyAnalytic;
use App\Models\SpotifyAnalyticsExport;
use App\Models\StatsExport;
use App\Models\Supporter;
use App\Models\Team;
use App\Models\UserEmailQueue;
use App\Models\UserOauth;
use App\Models\UserUpload;
use App\Models\VoucherRedemption;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Usr
 * 
 * @property int $usr_id
 * @property int $role_id
 * @property string|null $username
 * @property string|null $passwd
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $name_title
 * @property string|null $telephone
 * @property string|null $telefax
 * @property string|null $email
 * @property string|null $url
 * @property string|null $organisation
 * @property string|null $department
 * @property string|null $street
 * @property string|null $housenumber
 * @property string|null $feed_email
 * @property string|null $city
 * @property string|null $region
 * @property string|null $country
 * @property string|null $post_code
 * @property string|null $gender
 * @property int|null $is_acct_active
 * @property bool $is_trial
 * @property Carbon|null $date_created
 * @property Carbon|null $date_trialend
 * @property int|null $created_by
 * @property Carbon|null $last_updated
 * @property int|null $updated_by
 * @property int|null $package_id
 * @property Carbon|null $terms_date
 * @property float|null $terms_version
 * @property Carbon|null $privacy_date
 * @property float|null $privacy_version
 * @property float $funds
 * @property int $forum_number_post
 * @property string|null $register_court
 * @property string|null $register_number
 * @property string|null $vat_id
 * @property string|null $board
 * @property string|null $chairman
 * @property string|null $representative
 * @property string|null $mediarepresentative
 * @property string|null $controlling_authority
 * @property string|null $additional_specifications
 * @property int $is_updating
 * @property int $is_blocked
 * @property int $is_protected
 * @property int|null $parent_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property bool $is_supporter
 * @property bool $is_advertiser
 * @property bool $can_pay_by_bill
 * @property bool $has_paid
 * @property bool $super
 * @property string|null $name
 * @property string|null $avatar
 * @property array|null $preferences
 * @property Carbon|null $last_login
 * @property bool $agency_enabled
 * @property bool $audiotakes_enabled
 * @property int|null $new_package_id
 * @property int $welcome_email_state
 * @property int $available_stats_exports
 * @property bool $use_new_statistics
 * 
 * @property Collection|AudiotakesBankTransfer[] $audiotakes_bank_transfers
 * @property Collection|AudiotakesContractPartner[] $audiotakes_contract_partners
 * @property Collection|AudiotakesId[] $audiotakes_ids
 * @property Collection|AudiotakesPayoutContact[] $audiotakes_payout_contacts
 * @property Collection|AudiotakesPayout[] $audiotakes_payouts
 * @property Collection|AudiotakesPodcasterTransfer[] $audiotakes_podcaster_transfers
 * @property Collection|CampaignInvitation[] $campaign_invitations
 * @property Collection|CustomerPreference[] $customer_preferences
 * @property Collection|Feedback[] $feedback
 * @property Collection|Member[] $members
 * @property Collection|PackageChange[] $package_changes
 * @property Collection|PlayerConfig[] $player_configs
 * @property Collection|PodcastRoulette[] $podcast_roulettes
 * @property Collection|Space[] $spaces
 * @property Collection|SpotifyAnalytic[] $spotify_analytics
 * @property Collection|SpotifyAnalyticsExport[] $spotify_analytics_exports
 * @property Collection|StatsExport[] $stats_exports
 * @property Collection|Supporter[] $supporters
 * @property Collection|Team[] $teams
 * @property Collection|UserEmailQueue[] $user_email_queues
 * @property Collection|UserOauth[] $user_oauths
 * @property Collection|UserUpload[] $user_uploads
 * @property Collection|VoucherRedemption[] $voucher_redemptions
 *
 * @package App\Models\Base
 */
class Usr extends Model
{
	use SoftDeletes;
	protected $table = 'usr';
	protected $primaryKey = 'usr_id';

	protected $casts = [
		'role_id' => 'int',
		'is_acct_active' => 'int',
		'is_trial' => 'bool',
		'created_by' => 'int',
		'updated_by' => 'int',
		'package_id' => 'int',
		'terms_version' => 'float',
		'privacy_version' => 'float',
		'funds' => 'float',
		'forum_number_post' => 'int',
		'is_updating' => 'int',
		'is_blocked' => 'int',
		'is_protected' => 'int',
		'parent_user_id' => 'int',
		'is_supporter' => 'bool',
		'is_advertiser' => 'bool',
		'can_pay_by_bill' => 'bool',
		'has_paid' => 'bool',
		'super' => 'bool',
		'preferences' => 'json',
		'agency_enabled' => 'bool',
		'audiotakes_enabled' => 'bool',
		'new_package_id' => 'int',
		'welcome_email_state' => 'int',
		'available_stats_exports' => 'int',
		'use_new_statistics' => 'bool'
	];

	protected $dates = [
		'date_created',
		'date_trialend',
		'last_updated',
		'terms_date',
		'privacy_date',
		'last_login'
	];

	public function audiotakes_bank_transfers()
	{
		return $this->hasMany(AudiotakesBankTransfer::class, 'user_id');
	}

	public function audiotakes_contract_partners()
	{
		return $this->hasMany(AudiotakesContractPartner::class, 'user_id');
	}

	public function audiotakes_ids()
	{
		return $this->hasMany(AudiotakesId::class, 'user_id');
	}

	public function audiotakes_payout_contacts()
	{
		return $this->hasMany(AudiotakesPayoutContact::class, 'user_id');
	}

	public function audiotakes_payouts()
	{
		return $this->hasMany(AudiotakesPayout::class, 'user_id');
	}

	public function audiotakes_podcaster_transfers()
	{
		return $this->hasMany(AudiotakesPodcasterTransfer::class, 'user_id');
	}

	public function campaign_invitations()
	{
		return $this->hasMany(CampaignInvitation::class, 'user_id');
	}

	public function customer_preferences()
	{
		return $this->hasMany(CustomerPreference::class, 'user_id');
	}

	public function feedback()
	{
		return $this->hasMany(Feedback::class, 'user_id');
	}

	public function members()
	{
		return $this->hasMany(Member::class, 'user_id');
	}

	public function package_changes()
	{
		return $this->hasMany(PackageChange::class, 'user_id');
	}

	public function player_configs()
	{
		return $this->hasMany(PlayerConfig::class, 'user_id');
	}

	public function podcast_roulettes()
	{
		return $this->hasMany(PodcastRoulette::class, 'user_id');
	}

	public function spaces()
	{
		return $this->hasMany(Space::class, 'user_id');
	}

	public function spotify_analytics()
	{
		return $this->hasMany(SpotifyAnalytic::class, 'user_id');
	}

	public function spotify_analytics_exports()
	{
		return $this->hasMany(SpotifyAnalyticsExport::class, 'user_id');
	}

	public function stats_exports()
	{
		return $this->hasMany(StatsExport::class, 'user_id');
	}

	public function supporters()
	{
		return $this->hasMany(Supporter::class, 'user_id');
	}

	public function teams()
	{
		return $this->hasMany(Team::class, 'owner_id');
	}

	public function user_email_queues()
	{
		return $this->hasMany(UserEmailQueue::class, 'user_id');
	}

	public function user_oauths()
	{
		return $this->hasMany(UserOauth::class, 'user_id');
	}

	public function user_uploads()
	{
		return $this->hasMany(UserUpload::class, 'user_id');
	}

	public function voucher_redemptions()
	{
		return $this->hasMany(VoucherRedemption::class, 'user_id');
	}
}
