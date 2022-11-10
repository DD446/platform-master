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

class GetPackageFeaturePremiumSubdomains extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testCannotGetPremiumSubdomainsForUserWithoutPackageFeature()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $a = get_package_feature_premium_subdomains($user->package, $user);

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

    public function testCanGetPremiumSubdomainsForUserWithPackageFeature()
    {
        $user = User::factory()->create(['package_id' => 4]);
        $a = get_package_feature_premium_subdomains($user->package, $user);

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

    public function testCanGetPremiumSubdomainsForUserWithPackageFeatureAndDefaultDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);

        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://123.podcaster.de',
                    'subdomain' => '123.podcaster'
                ]
            ]
        );
        $a = get_package_feature_premium_subdomains($user->package, $user);

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

    public function testCanGetPremiumSubdomainsForUserWithPackageFeatureAndShortDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);

        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://ab.podcaster.de',
                    'subdomain' => 'ab.podcaster'
                ]
            ]
        );
        $a = get_package_feature_premium_subdomains($user->package, $user);

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

    public function testCanGetPremiumSubdomainsForUserWithPackageFeatureAndCustomDomainInUse()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);

        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcastern.de',
                    'subdomain' => '12.podcastern'
                ]
            ]
        );
        $a = get_package_feature_premium_subdomains($user->package, $user);

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

}
