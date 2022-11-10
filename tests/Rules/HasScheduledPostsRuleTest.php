<?php
/**
 * User: fabio
 * Date: 15.07.20
 * Time: 09:54
 */

namespace Tests\Rules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Feed;
use App\Models\Package;
use App\Models\Show;
use App\Models\User;
use App\Rules\HasScheduledPostsRule;
use Tests\TestCase;

class HasScheduledPostsRuleTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function test_user_can_upgrade_with_no_scheduled_post_and_no_feed()
    {
        $user = User::factory()->create(['package_id' => 1]);
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasScheduledPostsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_upgrade_with_no_scheduled_post()
    {
        // TODO: Remove when legacy login is shut off
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 1]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcaster.de',
                    'subdomain' => '12.podcaster'
                ]
            ]
        );
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasScheduledPostsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_downgrade_with_no_scheduled_post()
    {
        // TODO: Remove when legacy login is shut off
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://12.podcaster.de',
                'subdomain' => '12.podcaster'
            ]
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasScheduledPostsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

    public function test_user_cannot_downgrade_with_scheduled_post()
    {
        // TODO: Remove when legacy login is shut off
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://12.podcaster.de',
                    'subdomain' => '12.podcaster'
                ],
                'entries' => [
                    [
                        'title' => $this->faker->text(254),
                        'guid' => get_guid('pod'),
                        'is_public' => Show::PUBLISH_FUTURE,
                    ]
                ]
            ]
        );
        $this->be($user);
        $newPackage = Package::find(--$user->package_id);
        $rule = new HasScheduledPostsRule($newPackage);
        $this->assertFalse($rule->passes('not_relevant','not_used'));
    }

    public function test_user_can_upgrade_with_scheduled_post()
    {
        // TODO: Remove when legacy login is shut off
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        Feed::factory()->create(['username' => $user->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://12.podcaster.de',
                'subdomain' => '12.podcaster'
            ],
                'entries' => [
                    [
                        'title' => $this->faker->text(254),
                        'guid' => get_guid('pod'),
                        'is_public' => Show::PUBLISH_FUTURE,
                    ]
                ]
            ]
        );
        $this->be($user);
        $newPackage = Package::find(++$user->package_id);
        $rule = new HasScheduledPostsRule($newPackage);
        $this->assertTrue($rule->passes('not_relevant','not_used'));
    }

}
