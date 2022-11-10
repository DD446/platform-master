<?php
/**
 * User: fabio
 * Date: 07.08.20
 * Time: 11:34
 */

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\Package;
use App\Models\User;
use App\Rules\CanUseTrackerRule;
use Tests\TestCase;

class CanUseTrackerRuleTest  extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testUserCanUpgradeWithNoTracking()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new CanUseTrackerRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function testUserCanUpgradeWithTrackingWhenNewPackageHasFeature()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->create([
            'username' => $user->username,
            'settings' => [
                'rms' => 1
            ]
        ]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new CanUseTrackerRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function testUserCannotDowngradeWithTrackingWhenNewPackageIsMissingFeature()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->create([
            'username' => $user->username,
            'settings' => [
                'rms' => 1
            ]
        ]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new CanUseTrackerRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

    public function testUserCanDowngradeWithTrackingWhenNewPackageHasFeature()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        Feed::factory()->create([
            'username' => $user->username,
            'settings' => [
                'rms' => 1
            ]
        ]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new CanUseTrackerRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

}
