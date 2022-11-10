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
use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use App\Models\UserExtra;
use Tests\TestCase;

class GetPackageFeatureMembersTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_can_get_package_feature_members_for_user_with_no_configurations_and_missing_feature()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $a = get_package_feature_members($user->package, $user);

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

    public function test_can_get_package_feature_members_for_user_with_configurations_default()
    {
        $now = Carbon::now();
        $ue = UserExtra::factory([
                //'usr_id' => $user,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 0,
                'date_created' => $now->subMonths(3),
                'date_start' => $now->subMonths(3),
                'date_end' => $now->subMonths(3)->addDays(30),
            ]
        );
        $user = User::factory()->has($ue)->create([
                'package_id' => 3,
            ]);
        $a = get_package_feature_members($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(1, $a['included']);
        $this->assertEquals(0, $a['extra']);
        $this->assertEquals(1, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(1, $a['available']);
    }

    public function test_can_get_package_feature_members_for_user_with_extra_configurations()
    {
        $now = Carbon::now();
        $ue = UserExtra::factory([
                //'usr_id' => $user,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 2,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $user = User::factory()->has($ue)->create([
                'package_id' => 4,
            ]);
        $a = get_package_feature_members($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(3, $a['included']);
        $this->assertEquals(2, $a['extra']);
        $this->assertEquals(5, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(5, $a['available']);
    }

    public function test_can_get_package_feature_members_for_user_with_multiple_extra_configurations()
    {
        $user = User::factory()->create(['package_id' => 4]);
        $now = Carbon::now();
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 10,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $a = get_package_feature_members($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(3, $a['included']);
        $this->assertEquals(20, $a['extra']);
        $this->assertEquals(23, $a['total']);
        $this->assertEquals(0, $a['used']);
        $this->assertEquals(23, $a['available']);
    }

    public function test_can_get_package_feature_members_for_user_with_used_configurations()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        $now = Carbon::now();
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 10,
                'date_created' => $now,
                'date_start' => $now,
                'date_end' => $now->addDays(30),
            ]
        );
        $team = Team::factory()->create(
            [
                'owner_id' => $user->user_id,
            ]
        );
        $user2 = User::factory()->create();
        Member::factory()->create(
            [
                'team_id' => $team->id,
                'user_id' => $user2->user_id,
            ]
        );
        $user3 = User::factory()->create();
        Member::factory()->create(
            [
                'team_id' => $team->id,
                'user_id' => $user3->user_id,
            ]
        );

        $a = get_package_feature_members($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(3, $a['included']);
        $this->assertEquals(20, $a['extra']);
        $this->assertEquals(23, $a['total']);
        $this->assertEquals(2, $a['used']);
        $this->assertEquals(21, $a['available']);
    }
}
