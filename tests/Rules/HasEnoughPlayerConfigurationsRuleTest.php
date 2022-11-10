<?php
/**
 * User: fabio
 * Date: 15.07.20
 * Time: 10:11
 */

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Package;
use App\Models\PlayerConfig;
use App\Models\User;
use App\Models\UserExtra;
use App\Rules\HasEnoughPlayerConfigurationsRule;
use Tests\TestCase;

class HasEnoughPlayerConfigurationsRuleTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_can_upgrade_with_no_player_configurations()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_no_player_configurations()
    {
        $user = User::factory()->create(['package_id' => 3]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_no_player_configurations_to_package_without_player_configurations()
    {
        $user = User::factory()->create(['package_id' => 3]);
        $this->be($user);
        $newPackage = Package::find(1);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_without_player_configurations_with_player_configuration()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        PlayerConfig::factory()->create(
            [
                'user_id' => $user->user_id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(1);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_player_configurations_with_player_configuration()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        PlayerConfig::factory()->create(
            [
                'user_id' => $user->user_id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_player_configurations_with_player_configurations_and_extras()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_PLAYERCONFIGURATION,
                'extras_count' => 1,
                'date_created' => now(),
                'date_start' => now(),
                'date_end' => now()->addDays(30),
            ]
        );
        PlayerConfig::factory()->count(11)->create(
            [
                'user_id' => $user->user_id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_with_player_configurations_with_player_configurations_and_extras()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        UserExtra::factory()->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_PLAYERCONFIGURATION,
                'extras_count' => 1,
                'date_created' => now(),
                'date_start' => now(),
                'date_end' => now()->addDays(30),
            ]
        );
        PlayerConfig::factory()->count(22)->create(
            [
                'user_id' => $user->user_id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughPlayerConfigurationsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

}
