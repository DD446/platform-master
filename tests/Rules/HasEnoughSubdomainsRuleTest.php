<?php

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\Package;
use App\Models\User;
use App\Rules\HasEnoughBlogs;
use App\Rules\HasEnoughSubdomains;
use Tests\TestCase;

class HasEnoughSubdomainsRuleTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_can_upgrade_to_bigger_package_with_no_subdomains_created()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_smaller_package_with_no_subdomains_created()
    {
        $user = User::factory()->create(['package_id' => 2]);
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_without_subdomain_feature_having_subdomain()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://' . $user->username . '123.podcaster.de',
                'subdomain' => $user->username . '123.podcaster']
            ]
        );
        $this->be($user);
        $countFeeds = Feed::whereUsername($user->username)->count();
        $this->assertEquals(1, $countFeeds);
        $newPackage = Package::find(--$user->package_id);
        $this->assertNotEmpty($newPackage);
        $this->assertIsObject($newPackage);
        $this->assertEquals(1, $newPackage->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertIsObject($rule);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_subdomain_feature_having_subdomain()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://' . $user->username . '123.podcaster.de',
                'subdomain' => $user->username . '123.podcaster']
            ]
        );
        $this->be($user);
        $countFeeds = Feed::whereUsername($user->username)->count();
        $this->assertEquals(1, $countFeeds);
        $newPackage = Package::find(--$user->package_id);
        $this->assertNotEmpty($newPackage);
        $this->assertIsObject($newPackage);
        $this->assertEquals(2, $newPackage->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertIsObject($rule);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_to_package_with_subdomain_feature_having_subdomains()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->count(2)->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://' . $user->username . '123.podcaster.de',
                'subdomain' => $user->username . '123.podcaster']
            ]
        );
        $this->be($user);
        $countFeeds = Feed::whereUsername($user->username)->count();
        $this->assertEquals(2, $countFeeds);
        $newPackage = Package::find(--$user->package_id);
        $this->assertNotEmpty($newPackage);
        $this->assertIsObject($newPackage);
        $this->assertEquals(2, $newPackage->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertIsObject($rule);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_to_package_with_subdomain_feature_having_too_many_subdomains()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->count(4)->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://' . $user->username . '123.podcaster.de',
                'subdomain' => $user->username . '123.podcaster']
            ]
        );
        $this->be($user);
        $countFeeds = Feed::whereUsername($user->username)->count();
        $this->assertEquals(4, $countFeeds);
        $newPackage = Package::find(--$user->package_id);
        $this->assertNotEmpty($newPackage);
        $this->assertIsObject($newPackage);
        $this->assertEquals(2, $newPackage->package_id);
        $rule = new HasEnoughSubdomains($newPackage);
        $this->assertIsObject($rule);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }
}
