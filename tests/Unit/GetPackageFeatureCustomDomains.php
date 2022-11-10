<?php
/**
 * User: fabio
 * Date: 18.09.20
 * Time: 17:00
 */

namespace Tests\Unit;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\User;
use Tests\TestCase;

class GetPackageFeatureCustomDomains extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testCannotGetCustomDomainForUserWithoutPackageFeature()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $a = get_package_feature_custom_domains($user->package, $user);

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

    public function testCanGetCustomDomainForUserWithPackageFeature()
    {
        $user = User::factory()->create(['package_id' => 3]);
        $a = get_package_feature_custom_domains($user->package, $user);

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

    public function testCanGetCustomDomainForUserWithPackageFeatureAndDefaultDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);

        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcaster.de',
                    'subdomain' => '12.podcaster'
                ]
            ]
        );
        $a = get_package_feature_custom_domains($user->package, $user);

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

    public function testCanGetCustomDomainForUserWithPackageFeatureAndCustomDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);

        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => true,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcastern.de',
                    'subdomain' => '12.podcastern'
                ]
            ]
        );
        $a = get_package_feature_custom_domains($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(1, $a['included']);
        $this->assertEquals(0, $a['extra']);
        $this->assertEquals(1, $a['total']);
        $this->assertEquals(1, $a['used']);
        $this->assertEquals(0, $a['available']);
    }

    public function testCanGetCustomDomainsForUserWithPackageFeatureAndCustomDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        Feed::factory()->count(2)->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => true,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcastern.de',
                    'subdomain' => '12.podcastern'
                ]
            ]
        );
        $a = get_package_feature_custom_domains($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(3, $a['included']);
        $this->assertEquals(0, $a['extra']);
        $this->assertEquals(3, $a['total']);
        $this->assertEquals(1, $a['used']);
        $this->assertEquals(2, $a['available']);
    }

    public function testCanGetDifferentCustomDomainsForUserWithPackageFeatureAndCustomDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => true,
                    'tld' => 'de',
                    'hostname' => 'https://123.podcastern.de',
                    'subdomain' => '123.podcastern'
                ]
            ]
        );
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => true,
                    'tld' => 'de',
                    'hostname' => 'https://456.podcastern.de',
                    'subdomain' => '456.podcastern'
                ]
            ]
        );
        $a = get_package_feature_custom_domains($user->package, $user);

        $this->assertIsArray($a);
        $this->assertArrayHasKey('included', $a);
        $this->assertArrayHasKey('extra', $a);
        $this->assertArrayHasKey('total', $a);
        $this->assertArrayHasKey('used', $a);
        $this->assertArrayHasKey('available', $a);
        $this->assertEquals(3, $a['included']);
        $this->assertEquals(0, $a['extra']);
        $this->assertEquals(3, $a['total']);
        $this->assertEquals(2, $a['used']);
        $this->assertEquals(1, $a['available']);
    }

}
