<?php
/**
 * User: fabio
 * Date: 16.07.20
 * Time: 07:59
 */

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Classes\Activity;
use App\Models\Package;
use App\Models\User;
use App\Models\UserAccounting;
use App\Rules\HasEnoughFundsRule;
use Tests\TestCase;

class HasEnoughFeedsRuleTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_cannot_upgrade_with_no_funds()
    {
        $user = User::factory()->create(['package_id' => 1, 'funds' => 0]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
        $packageName = trans_choice('package.package_name', $newPackage->package_name);
        $this->assertMatchesRegularExpression('/.*`' . $packageName . '`.*/', $rule->message());
    }

    public function test_user_can_upgrade_with_exact_funds()
    {
        $user = User::factory()->create(['package_id' => 1, 'funds' => 5.0]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_upgrade_with_enough_funds()
    {
        $user = User::factory()->create(['package_id' => 1, 'funds' => 15.0]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_upgrade_with_funds_from_last_order()
    {
        $this->withoutEvents();
        $this->withoutNotifications();

        $user = User::factory()->create(['package_id' => 1, 'funds' => 0]);
        UserAccounting::factory()->create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'activity_characteristic' => 1,
            'activity_description' => 'Buchung Paket Starter',
            'amount' => -6.0,
            'currency' => 'EUR',
            'date_created' => now()->subDays(1),
            'date_start' => now()->subDays(1),
            'date_end' => now()->addDays(29),
            'procedure' => 1,
            'status' => 1,
        ]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_upgrade_with_not_enough_funds_from_order()
    {
        $this->withoutEvents();
        $this->withoutNotifications();

        $user = User::factory()->create(['package_id' => 1, 'funds' => 0]);
        UserAccounting::factory()->create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'activity_characteristic' => 1,
            'activity_description' => 'Buchung Paket Starter',
            'amount' => -6.0,
            'currency' => 'EUR',
            'date_created' => now()->subDays(1),
            'date_start' => now()->subDays(20),
            'date_end' => now()->addDays(10),
            'procedure' => 1,
            'status' => 1,
        ]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
        $packageName = trans_choice('package.package_name', $newPackage->package_name);
        $this->assertMatchesRegularExpression('/.*`' . $packageName . '`.*/', $rule->message());
    }

    public function test_user_cannot_downgrade_with_no_funds()
    {
        $user = User::factory()->create(['package_id' => 2, 'funds' => 0]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
        $packageName = trans_choice('package.package_name', $newPackage->package_name);
        $this->assertMatchesRegularExpression('/.*`' . $packageName . '`.*/', $rule->message());
    }

    public function test_user_cannot_downgrade_with_negative_funds()
    {
        $user = User::factory()->create(['package_id' => 2, 'funds' => -3.0]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
        $packageName = trans_choice('package.package_name', $newPackage->package_name);
        $this->assertMatchesRegularExpression('/.*`' . $packageName . '`.*/', $rule->message());
    }

    public function test_user_can_downgrade_with_exact_funds()
    {
        $user = User::factory()->create(['package_id' => 2, 'funds' => 1.0]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_enough_funds()
    {
        $user = User::factory()->create(['package_id' => 2, 'funds' => 15.0]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_funds_from_last_order()
    {
        $this->withoutEvents();
        $this->withoutNotifications();

        $user = User::factory()->create(['package_id' => 2, 'funds' => 0]);
        UserAccounting::factory()->create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'activity_characteristic' => 1,
            'activity_description' => 'Buchung Paket Starter',
            'amount' => -5.0,
            'currency' => 'EUR',
            'date_created' => now()->subDays(1),
            'date_start' => now()->subDays(1),
            'date_end' => now()->addDays(29),
            'procedure' => 1,
            'status' => 1,
        ]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_with_not_enough_funds_from_too_old_order()
    {
        $this->withoutEvents();
        $this->withoutNotifications();

        $user = User::factory()->create(['package_id' => 2, 'funds' => 0]);
        UserAccounting::factory()->create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'activity_characteristic' => 1,
            'activity_description' => 'Buchung Paket Starter',
            'amount' => -5.0,
            'currency' => 'EUR',
            'date_created' => now()->subDays(1),
            'date_start' => now()->subDays(29),
            'date_end' => now()->addDays(1),
            'procedure' => 1,
            'status' => 1,
        ]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughFundsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
        $packageName = trans_choice('package.package_name', $newPackage->package_name);
        $this->assertMatchesRegularExpression('/.*`' . $packageName . '`.*/', $rule->message());
    }

}
