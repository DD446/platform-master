<?php
/**
 * User: fabio
 * Date: 04.08.20
 * Time: 14:49
 */

namespace Tests\Classes;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Classes\UserPaymentManager;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserExtra;
use App\Models\UserPayment;
use Tests\TestCase;

class UserAccountingManagerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testAddPackageBooking()
    {
        $user = User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => User::IS_TRIAL,
            'funds' => 12,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now()->subYear(),
            'date_trialend' => Carbon::now()->subYear()->addMonth(),
        ]);

        $uam = new UserAccountingManager();
        $dateStart = now()->micro(0);
        $uam->add($user, Activity::PACKAGE, $user->package_id,
            change_prefix($user->package->monthly_cost*$user->package->paying_rhythm), null,
            UserPayment::CURRENCY_DEFAULT, $dateStart);

        $_user = User::find($user->user_id);

        $this->assertEquals(11, $_user->funds);
        $this->assertDatabaseHas('user_accounting', ['usr_id' => $user->user_id]);

        $packageName = trans_choice('package.package_name', $user->package->package_name);
        $activityDescription = trans('bills.accounting_activity_booking', ['details' => $packageName]);

        $ua = UserAccounting::first();
        $this->assertEquals(2, $ua->activity_type);
        $this->assertEquals(Activity::PACKAGE, $ua->activity_type);
        $this->assertEquals(1, $ua->activity_characteristic);
        $this->assertEquals(User::HAS_PAID, $_user->is_trial);
        $this->assertEquals(0, $_user->is_blocked);
        $this->assertEquals($user->package_id, $ua->activity_characteristic);
        $this->assertEquals("Buchung Paket Starter", $ua->activity_description);
        $this->assertEquals($activityDescription, $ua->activity_description);
        $this->assertEquals($user->user_id, $ua->usr_id);
        $this->assertEquals(-1.00, $ua->amount);
        $this->assertEquals('EUR', $ua->currency);
        $this->assertEquals(UserPayment::CURRENCY_DEFAULT, $ua->currency);
        $this->assertEquals($dateStart, $ua->date_start);
        $this->assertEquals($dateStart->addDays(30), $ua->date_end);
    }

    public function testAddPackageBookingWithZeroFundsWhichBlocksUser()
    {
        $user = User::factory()->create([
            'package_id' => 2,
            'is_acct_active' => 1,
            'is_trial' => User::IS_INACTIVE,
            'funds' => 4,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now()->subMonths(3),
            'date_trialend' => Carbon::now()->subMonths(3)->addMonth(),
        ]);

        $uam = new UserAccountingManager();
        $dateStart = now()->micro(0);
        $uam->add($user, Activity::PACKAGE, $user->package_id,
            change_prefix($user->package->monthly_cost*$user->package->paying_rhythm), null,
            UserPayment::CURRENCY_DEFAULT, $dateStart);

        $_user = User::find($user->user_id);

        $this->assertEquals(User::HAS_PAID, $_user->funds);
        $this->assertDatabaseHas('user_accounting', ['usr_id' => $user->user_id]);

        $pn = $user->package->package_name;
        $packageName = trans_choice('package.package_name', $user->package->package_name);
        $activityDescription = trans('bills.accounting_activity_booking', ['details' => $packageName]);

        $ua = UserAccounting::first();
        $this->assertEquals(2, $ua->activity_type);
        $this->assertEquals(Activity::PACKAGE, $ua->activity_type);
        $this->assertEquals(2, $ua->activity_characteristic);
        $this->assertEquals($user->package_id, $ua->activity_characteristic);
        $this->assertEquals("Buchung Paket Podcaster", $ua->activity_description);
        $this->assertEquals($activityDescription, $ua->activity_description);
        $this->assertEquals($user->user_id, $ua->usr_id);
        $this->assertEquals($_user->user_id, $ua->usr_id);
        $this->assertEquals(-5.00, $ua->amount);
        $this->assertEquals('EUR', $ua->currency);
        $this->assertEquals(UserPayment::CURRENCY_DEFAULT, $ua->currency);
        $this->assertEquals($dateStart, $ua->date_start);
        $this->assertEquals($dateStart->addDays(30), $ua->date_end);
        $this->assertEquals(User::HAS_PAID, $_user->is_trial);
        $this->assertEquals(1, $_user->is_blocked);
    }

    public function testTrackOrderOneFeed()
    {
        $this->withoutNotifications();

        $user = User::factory()->create();
        $ue = UserExtra::factory()->create([
            'usr_id' => $user->user_id,
            'extras_type' => UserExtra::EXTRA_FEED,
            'extras_count' => 1,
        ]);

        $this->assertDatabaseHas('user_extras', ['usr_id' => $ue->usr_id]);
        $_ue = UserExtra::first();
        $this->assertEquals($user->user_id, $ue->usr_id);
        $this->assertEquals(UserExtra::EXTRA_FEED, $ue->extras_type);

        $uam = new UserAccountingManager();
        $uam->trackOrder($ue);

        $this->assertDatabaseHas('user_accounting', ['usr_id' => $ue->usr_id]);

        $ua = UserAccounting::first();
        $this->assertEquals($user->user_id, $ua->usr_id);
        $this->assertEquals(UserExtra::EXTRA_FEED, $ua->activity_characteristic);
        $this->assertEquals(Activity::EXTRAS, $ua->activity_type);
        $this->assertEquals(-1.0, $ua->amount);

    }

    public function testTrackOrderExtraSpace()
    {
        $this->withoutNotifications();

        $user = User::factory()->create();
        $ue = UserExtra::factory()->create([
            'usr_id' => $user->user_id,
            'extras_type' => UserExtra::EXTRA_STORAGE,
            'extras_count' => 2,
        ]);

        $this->assertDatabaseHas('user_extras', ['usr_id' => $ue->usr_id]);

        $uam = new UserAccountingManager();
        $uam->trackOrder($ue);

        $this->assertDatabaseHas('user_accounting', ['usr_id' => $ue->usr_id]);

        $ua = UserAccounting::first();
        $this->assertEquals($user->user_id, $ua->usr_id);
        $this->assertEquals(UserExtra::EXTRA_STORAGE, $ua->activity_characteristic);
        $this->assertEquals(Activity::EXTRAS, $ua->activity_type);
        $this->assertEquals(-2.0, $ua->amount);

    }

    public function testGetDueCustomers()
    {
        $uam = new UserAccountingManager();
        $aUsers = $uam->getDueCustomers();

        $this->assertIsArray($aUsers);
    }

    public function testGetDueCustomerAfterTrialIsFinished()
    {
        User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => 1,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now()->subMonths(2),
            'date_trialend' => Carbon::now()->subMonth(),
        ]);
        User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => 1,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now(),
            'date_trialend' => Carbon::now()->addMonth(),
        ]);

        $uam = new UserAccountingManager();
        $aUsers = $uam->getDueCustomers();

        $this->assertIsArray($aUsers);
        $this->assertCount(1, $aUsers);
    }

    public function testGetDueCustomerAfterTrialIsFinishedWithFunds()
    {
        $expired = User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => 1,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now()->subMonths(2),
            'date_trialend' => Carbon::now()->subMonth(),
        ]);
        UserAccounting::factory()->create([
            'usr_id' => $expired->user_id,
            'activity_type' => Activity::FUNDS,
        ]);
        User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => 1,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now(),
            'date_trialend' => Carbon::now()->addMonth(),
        ]);

        $uam = new UserAccountingManager();
        $aUsers = $uam->getDueCustomers();

        $this->assertIsArray($aUsers);
        $this->assertCount(1, $aUsers);
    }

    public function testGetDueCustomer()
    {
        $user = User::factory()->create([
            'package_id' => 1,
            'is_acct_active' => 1,
            'is_trial' => 1,
            'role_id' => User::ROLE_USER,
            'date_created' => Carbon::now()->subYear(),
            'date_trialend' => Carbon::now()->subYear()->addMonth(),
        ]);
        UserAccounting::factory()->create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'date_end' => Carbon::now()->subMinutes(3),
        ]);

        $uam = new UserAccountingManager();
        $aUsers = $uam->getDueCustomers();

        $this->assertIsArray($aUsers);
        $this->assertCount(1, $aUsers);
    }

    public function testGetRefundForAllDays()
    {
        $this->withoutNotifications();

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now(),
            'date_end' => now()->addDays(30),
            'amount' => -5.0
        ]);

        $uam = new UserAccountingManager();
        $refunds = $uam->getRefund($ua);

        $this->assertEquals(5, $refunds);
    }

    public function testGetRefundForTenDays()
    {
        $this->withoutNotifications();

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now()->subDays(20),
            'date_end' => now()->addDays(10),
            'amount' => -12.0
        ]);

        $uam = new UserAccountingManager();
        $refunds = $uam->getRefund($ua);

        $this->assertEquals(4, $refunds);
    }

    public function testGetRefundForOneDayForCorporate()
    {
        $this->withoutNotifications();

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now()->subDays(29),
            'date_end' => now()->addDay(),
            'amount' => -100.0
        ]);

        $uam = new UserAccountingManager();
        $refunds = $uam->getRefund($ua);

        $this->assertEquals(3.33, $refunds);
    }

    public function testGetRefundForOneDayForProfessional()
    {
        $this->withoutNotifications();

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now()->subDays(29),
            'date_end' => now()->addDay(),
            'amount' => -10.0
        ]);

        $uam = new UserAccountingManager();
        $refunds = $uam->getRefund($ua);

        $this->assertEquals(0.33, $refunds);
    }

    public function testChangePackageForActiveUserInTrialUpgradeFromStarterToPodcaster()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 0,
                'is_trial' => 1,
                'is_blocked' => 0,
            ]
        );
        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 2);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', ['usr_id' => $user->user_id]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(2, $_user->package_id);
        $this->assertEquals(-5.0, $_user->funds);
        $this->assertEquals(true, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserInTrialUpgradeFromPodcasterToMaxi()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 2,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 0,
                'is_trial' => 1,
                'is_blocked' => 0,
            ]
        );
        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 4);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', ['usr_id' => $user->user_id]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(4, $_user->package_id);
        $this->assertEquals(-20.0, $_user->funds);
        $this->assertEquals(true, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserUpgradeFromMaxiToCorporate()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 4,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 100,
                'is_trial' => -1,
                'is_blocked' => 0,
            ]
        );
        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 6);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', ['usr_id' => $user->user_id]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(6, $_user->package_id);
        $this->assertEquals(0, $_user->funds);
        $this->assertEquals(false, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserUpgradeFromStarterToProfessionalWithRefund()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 9.03,
                'is_trial' => -1,
                'is_blocked' => 0,
            ]
        );

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now()->subDays(1),
            'date_end' => now()->addDays(29),
            'amount' => -1.0,
            'usr_id' => $user->user_id,
        ]);

        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 3);
        $this->assertTrue($res);


        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::REFUND
        ]);
        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE
        ]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(3, $_user->package_id);
        $this->assertEquals(0, $_user->funds);
        $this->assertEquals(false, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserDowngradeFromPodcasterToStarterWithRefund()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 2,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 0,
                'is_trial' => -1,
                'is_blocked' => 0,
            ]
        );

        $ua = UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now()->subDays(15),
            'date_end' => now()->addDays(15),
            'amount' => -5.0,
            'usr_id' => $user->user_id,
        ]);

        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 1);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::REFUND
        ]);
        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE
        ]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(1, $_user->package_id);
        $this->assertEquals(1.5, $_user->funds);
        $this->assertEquals(false, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserUpgradeFromStarterToPodcasterWithRefundOnSameDay()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 4,
                'is_trial' => -1,
                'is_blocked' => 0,
            ]
        );

        UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now(),
            'date_end' => now()->addDays(30),
            'amount' => -1.0,
            'usr_id' => $user->user_id,
            'currency' => 'EUR'
        ]);

        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 2);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::REFUND
        ]);
        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE
        ]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(2, $_user->package_id);
        $this->assertEquals(0, $_user->funds);
        $this->assertEquals(false, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    public function testChangePackageForActiveUserUpgradeFromStarterToProfessionalAndDowngradeToPodcasterOnSameDay()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 9,
                'is_trial' => -1,
                'is_blocked' => 0,
            ]
        );

        UserAccounting::factory()->create([
            'activity_type' => Activity::PACKAGE,
            'date_start' => now(),
            'date_end' => now()->addDays(30),
            'amount' => -1.0,
            'usr_id' => $user->user_id,
            'currency' => 'EUR'
        ]);

        $uam = new UserAccountingManager();
        $res = $uam->changePackage($user, 3);
        $this->assertTrue($res);
        $res = $uam->changePackage($user, 2);
        $this->assertTrue($res);

        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::REFUND
        ]);
        $this->assertDatabaseHas('user_accounting', [
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE
        ]);
        $this->assertDatabaseHas('usr', ['usr_id' => $user->user_id]);

        $_user = User::find($user->user_id);
        $this->assertEquals(2, $_user->package_id);
        $this->assertEquals(5.0, $_user->funds);
        $this->assertEquals(false, $_user->is_blocked);
        $this->assertEquals(-1, $_user->is_trial);
    }

    // User added funds during trial period
    // rest of trial period was granted as free time
    // User switches package during this free time
    public function testChangePackageDuringTrial()
    {
        $this->withoutNotifications();

        $user = User::factory()->create(
            [
                'package_id' => 1,
                'is_acct_active' => 1,
                'role_id' => User::ROLE_USER,
                'funds' => 0,
                'is_trial' => 1,
                'is_blocked' => 0,
                'date_created' => now()->subDays(15),
                'date_trialend' => now()->addDays(15),
            ]
        );

        $this->assertDatabaseHas('usr', [
            'usr_id' => $user->user_id,
            'is_trial' => 1,
            'funds' => 0,
        ]);

        $upm = new UserPaymentManager();
        $up = $upm->add($user, $user->user_id, 15, UserPayment::CURRENCY_DEFAULT, UserPayment::PAYMENT_METHOD_LOCALBANK);

        $this->assertIsObject($up);
        $this->assertDatabaseHas('user_payments', [
            'payer_id' => $user->user_id,
            'receiver_id' => $user->user_id,
            'amount' => 15,
            'currency' => UserPayment::CURRENCY_DEFAULT,
        ]);

        $_user = User::findOrFail($user->user_id);

        $this->assertEquals(15, $_user->funds);
        $this->assertIsNotBool($_user->is_trial);
        $this->assertEquals(-1, $_user->is_trial);
        $this->assertIsBool($_user->is_blocked);
        $this->assertEquals(false, $_user->is_blocked);

        $uam = new UserAccountingManager();
        $uam->changePackage($_user, 2);

        $this->assertEquals(2, $_user->package_id);
        $this->assertEquals(15, $_user->funds);
    }
}
