<?php
/**
 * User: fabio
 * Date: 18.09.20
 * Time: 17:42
 */

namespace Tests\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\User;
use Tests\TestCase;

class FeedControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testDomainOptions()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 1]);
        Feed::factory()->create([
            'username' => $user->username,
            'feed_id' => 'test'
        ]);
        $this->be($user);

        $response = $this->json('GET', '/beta/domains/user?feedId=test');

        $response->assertStatus(200)
            ->assertJson([
               'feeds' => [],
               'tlds' => [],
               'hostdomains' => [],
            ]);
    }

    public function testDomainOptionsWithCustomDomain()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 3]);
        Feed::factory()->create([
            'username' => $user->username,
            'feed_id' => 'test',
            'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => true,
                'tld' => 'org',
                'hostname' => 'https://podcast.example.org',
                'subdomain' => 'podcast.example',
                'domain' => 'podcast.example.org',
            ]
        ]);
        Feed::factory()->create([
            'username' => $user->username,
            'feed_id' => 'test2',
            'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => true,
                'tld' => 'org',
                'hostname' => 'https://test2.brand.org',
                'subdomain' => 'test2.brand',
                'domain' => 'test2.brand.org',
            ]
        ]);
        $this->be($user);

        $response = $this->json('GET', '/beta/domains/user?feedId=test&type=custom');

        $response->assertStatus(200)
            ->assertJson([
               'feeds' => [
                    [
                        'website_type' => 'wordpress',
                       'is_custom' => true,
                       'tld' => 'org',
                       'hostname' => 'https://test2.brand.org',
                       'subdomain' => 'test2.brand',
                       'domain' => 'org',
                       'stripped_subdomain' => 'test2.brand',
                       'name' => 'test2.brand.org',
                    ]
               ],
               'tlds' => [
                   [
                       'value' => 'com',
                       'text' => '.org',
                   ]
               ],
                'hostdomains' => [],
            ]);
    }

    public function testGetTlds()
    {
        $this->withoutEvents();
        $user = User::factory()->create(['package_id' => 3]);
        $this->be($user);

        $response = $this->json('GET', '/beta/domains/tlds');
        $response->assertStatus(200)
            ->assertJson([]);
    }
}
