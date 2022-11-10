<?php
/**
 * User: fabio
 * Date: 15.07.20
 * Time: 10:11
 */

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Member;
use App\Models\Package;
use App\Models\PlayerConfig;
use App\Models\Team;
use App\Models\User;
use App\Models\UserExtra;
use App\Rules\HasEnoughMembersRule;
use Tests\TestCase;

class HasEnoughMembersRuleTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_can_upgrade_with_no_members()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_no_members()
    {
        $user = User::factory()->create(['package_id' => 3]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_no_members_to_package_without_members()
    {
        $user = User::factory()->create(['package_id' => 3]);
        $this->be($user);
        $newPackage = Package::find(1);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_without_members_with_members()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        $team = Team::factory()->create(
            [
                'owner_id' => $user->user_id,
            ]
        );
        Member::factory()->create(
            [
                'user_id' => User::factory()->create()->user_id,
                'team_id' => $team->id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(1);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_members_with_member()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        $team = Team::factory()->create(
            [
                'owner_id' => $user->user_id,
            ]
        );
        Member::factory()->create(
            [
                'user_id' => User::factory()->create()->user_id,
                'team_id' => $team->id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_members_with_members_and_extras()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        UserExtra::factory()->count(2)->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 1,
                'date_created' => now(),
                'date_start' => now(),
                'date_end' => now()->addDays(30),
            ]
        );
        $team = Team::factory()->create(
            [
                'owner_id' => $user->user_id,
            ]
        );
        Member::factory()->create(
            [
                'user_id' => User::factory()->create()->user_id,
                'team_id' => $team->id,
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_with_members_with_members_and_extras()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        UserExtra::factory()->create(
            [
                'usr_id' => $user->user_id,
                'extras_type' => UserExtra::EXTRA_MEMBER,
                'extras_count' => 1,
                'date_created' => now(),
                'date_start' => now(),
                'date_end' => now()->addDays(30),
            ]
        );
        $team = Team::factory()->create(
            [
                'owner_id' => $user->user_id,
            ]
        );
        for($i = 0; $i < 10; $i++) {
            Member::factory()->create(
                [
                    'user_id' => User::factory()->create()->user_id,
                    'team_id' => $team->id,
                ]
            );
        }
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughMembersRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

}
