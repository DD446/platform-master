<?php
/**
 * User: fabio
 * Date: 25.09.20
 * Time: 12:38
 */

namespace Tests\Classes;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Classes\DomainManager;
use App\Models\Feed;
use App\Models\User;
use Tests\TestCase;

class DomainManagerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testCheckCnameWithDummyDomain()
    {
        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->checkCname('www.example.com');
    }

    public function testCheckCnameWithDomainWithNoCname()
    {
        $dm = new DomainManager();

        $this->expectException('App\Exceptions\DomainException');
        $dm->checkCname('m.podcast.de');
    }

    public function testCheckCnameWithNonExistingDomain()
    {
        $dm = new DomainManager();

        $this->expectException('App\Exceptions\DomainException');
        $dm->checkCname('voelligfantastischistdashierfuereinendomainnamen.de');

        $this->expectException('App\Exceptions\DomainException');
        $dm->checkCname('podcast.österreich.at');
    }

    public function testCheckCnameWithUmlautDomainWithNoPunyCode()
    {
        $dm = new DomainManager();

        $this->expectException('App\Exceptions\DomainException');
        $dm->checkCname('podcast.österreich.at');
    }

    public function testCheckCname()
    {
        $dm = new DomainManager();

        $result = $dm->checkCname('www.biblenow.de');
        $this->assertEquals(true, $result);
    }

    public function testIsInvalidCustomDomainBecauseOfLocalDomainUsed()
    {
        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidCustomDomain('web.podcaster', 'de');
    }

    public function testIsInvalidCustomDomain()
    {
        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidCustomDomain('web.biblenow', 'de');
    }

    public function testIsValidCustomDomain()
    {
        $dm = new DomainManager();
        $result = $dm->isValidCustomDomain('www.biblenow', 'de');
        $this->assertEquals(true, $result);
    }

    public function testGetLocal()
    {
        $dm = new DomainManager();
        $localeDomains = $dm->getLocal();
        $defaultDomain = 'podcaster.de';

        $this->assertIsArray($localeDomains);
        $this->assertContains($defaultDomain, $localeDomains);
    }

    public function testIsLocal()
    {
        $dm = new DomainManager();
        $this->assertEquals(true, $dm->isLocal('podcaster.de'));
        $this->assertEquals(false, $dm->isLocal('podcaster.com'));
    }

    public function testIsInvalidLocalDomain()
    {
        $user = User::factory()->create(['package_id' => 2]);

        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidLocalDomain($user, 'podcast', 'podcaster.tld');
    }

    public function testIsInvalidLocalDomainWithOnlyPoint()
    {
        $user = User::factory()->create(['package_id' => 2]);

        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidLocalDomain($user, '.', 'podcaster.de');
    }

    public function testIsInvalidLocalDomainBecauseNotAvailable()
    {
        $this->withoutNotifications();
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 2]);
        $user2 = User::factory()->create([
            'package_id' => 2,
        ]);
        $feed = Feed::factory()->create(['username' => $user2->username, 'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://abc.podcaster.de',
                'subdomain' => 'abc.podcaster'
            ]
            ]
        );

        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidLocalDomain($user, 'abc', 'podcaster.de');
    }

    public function testIsAvailable()
    {
        $this->withoutNotifications();
        $this->withoutEvents();

        // Clean-up because this is saved in mongodb which is not resetted
        Feed::where('domain.hostname', 'https://abc.podcaster.de')->delete();
        $user = User::factory()->create(['package_id' => 2]);
        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://abc.podcaster.de',
                    'subdomain' => 'abc.podcaster'
                ]
            ]
        );

        $dm = new DomainManager();
        $result = $dm->isAvailable($user->username, 'abc.podcaster', 'de');
        $this->assertTrue($result);
    }

    public function testIsInvalidLocalDomainBecauseTooShort()
    {
        $user = User::factory()->create(['package_id' => 2]);

        $dm = new DomainManager();
        $this->expectException('App\Exceptions\DomainException');
        $dm->isValidLocalDomain($user, 'e', 'podcaster.de');
    }

    public function testIsValidLocalDomainWithSubdomain()
    {
        $user = User::factory()->create(['package_id' => 2]);

        $dm = new DomainManager();
        $result = $dm->isValidLocalDomain($user, 'eee', 'podcaster.de');
        $this->assertEquals(true, $result);
    }

    public function testIsValidLocalDomainWithShortSubdomain()
    {
        $user = User::factory()->create(['package_id' => 4]);

        $dm = new DomainManager();
        $result = $dm->isValidLocalDomain($user, 'ee', 'podcaster.de');
        $this->assertEquals(true, $result);
    }

    public function testIsValidLocalDomain()
    {
        $user = User::factory()->create(['package_id' => 2]);

        $dm = new DomainManager();
        $result = $dm->isValidLocalDomain($user, 'kosteneinziehung', 'podcaster.de');
        $this->assertEquals(true, $result);
    }

    public function testWriteConfig()
    {

    }
}
