<?php

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\Package;
use App\Models\User;
use App\Rules\HasEnoughBlogs;
use Tests\TestCase;

class HasEnoughBlogsRuleTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_can_upgrade_to_bigger_package_with_no_blogs()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughBlogs($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_smaller_package_with_no_blogs()
    {
        $user = User::factory()->create(['package_id' => 2]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughBlogs($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_smaller_package_with_one_blog()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->create(['username' => $user->username, 'domain' => ['website_type' => 'wordpress']]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $this->assertNotEmpty($newPackage);
        $this->assertIsObject($newPackage);
        $this->assertEquals(1, $newPackage->package_id);
        $rule = new HasEnoughBlogs($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_smaller_package_with_too_many_blogs()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->count(2)->create(['username' => $user->username, 'domain' => ['website_type' => 'wordpress']]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughBlogs($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }
}
