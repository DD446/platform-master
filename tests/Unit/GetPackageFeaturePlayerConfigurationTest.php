<?php
/**
 * User: fabio
 * Date: 15.07.20
 * Time: 10:14
 */

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\PlayerConfig;
use App\Models\User;
use App\Models\UserExtra;
use Tests\TestCase;

class GetPackageFeaturePlayerConfigurationTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_can_get_package_feature_player_configuration_for_user_with_no_configurations_and_missing_feature()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $a = get_package_feature_player_configuration($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(0, $a['included']);
        $this->assertEquals(0, $a['extra']);
        $this->assertEquals(0, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(0, $a['available']);
    }

    public function test_can_get_package_feature_player_configuration_for_user_with_extra_configurations()
    {
        $user = User::factory()->create(['package_id' => 4]);
        $now = Carbon::now();
        UserExtra::factory()->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_PLAYERCONFIGURATION,
                'extras_count' => 10,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $a = get_package_feature_player_configuration($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(50, $a['included']);
        $this->assertEquals(10, $a['extra']);
        $this->assertEquals(60, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(60, $a['available']);
    }

    public function test_can_get_package_feature_player_configuration_for_user_with_multiple_extra_configurations()
    {
        $user = User::factory()->create(['package_id' => 4]);
        $now = Carbon::now();
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_PLAYERCONFIGURATION,
                'extras_count' => 10,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $a = get_package_feature_player_configuration($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(50, $a['included']);
        $this->assertEquals(20, $a['extra']);
        $this->assertEquals(70, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(70, $a['available']);
    }

    public function test_can_get_package_feature_player_configuration_for_user_with_used_configurations()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        $now = Carbon::now();
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_PLAYERCONFIGURATION,
                'extras_count' => 10,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $pc = PlayerConfig::factory()->count(2)->create(
            [
                'user_id' => $user->user_id,
            ]
        );
/*        $pc->user()->associate($user);
        $pc->save();*/

        $a = get_package_feature_player_configuration($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(50, $a['included']);
        $this->assertEquals(20, $a['extra']);
        $this->assertEquals(70, $a['total']);
        $this->assertEquals(2, $a['used']);
        $this->assertEquals(68, $a['available']);
    }
}
