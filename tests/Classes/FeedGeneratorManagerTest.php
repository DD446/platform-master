<?php
/**
 * User: fabio
 * Date: 26.09.20
 * Time: 08:28
 */

namespace Tests\Classes;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use App\Classes\FeedGeneratorManager;
use App\Models\Feed;
use App\Models\User;
use Laminas\Feed\Reader\Reader;
use Tests\TestCase;

class FeedGeneratorManagerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');
    }

    public function testGetDomain()
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

        $fgm = new FeedGeneratorManager($user->username, $feed->feed_id);
        $domain = $fgm->getDomain($user->username, $feed->feed_id);

        $this->assertIsArray($domain);
        $this->assertArrayHasKey('hostname', $domain);
        $this->assertEquals('https://123.podcaster.de', $domain['hostname']);
    }

    public function testLink()
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
        $this->withoutEvents();
        $fgm = new FeedGeneratorManager($user->username, $feed->feed_id);
        $filename = 'test.mp3';
        $path = storage_path(get_user_media_path($user->username));
        $filepath = $path . DIRECTORY_SEPARATOR . $filename;
        $file = [
            'id'    => 1,
            'name'  => $filename, // File name
            'path'  => $filepath, // Complete path and file name
            'byte'  => 1, // Size in bytes
        ];
        $publicDir = 'download';
        File::makeDirectory($path, 0755, true);
        file_put_contents($filepath, "1");
        $result = $fgm->link($user->username, $file, $feed->feed_id, $publicDir);

        $this->assertEquals(true, $result);
    }

    public function testPublish()
    {
        $this->withoutEvents();

        $user = User::factory()->create(['package_id' => 4]);
        $feed = Feed::factory()->create(['username' => $user->username, 'domain' => [
                    'website_type' => 'wordpress',
                    'is_custom' => false,
                    'tld' => 'de',
                    'hostname' => 'https://123.podcaster.de',
                    'subdomain' => '123.podcaster',
                    'protocol' => 'https',
                ]
            ]
        );
        $this->withoutEvents();
        $fgm = new FeedGeneratorManager($user->username, $feed->feed_id);
        $result = $fgm->publish();
        $feedPath = storage_path(get_user_feed_path($user->username)) . DIRECTORY_SEPARATOR . $feed->feed_id . '.rss';
        $this->assertFileExists($feedPath);
    }

    public function testFeedHasValidEntries()
    {
        date_default_timezone_set('Europe/Berlin');
        $this->withoutEvents();

        $now = new \DateTimeImmutable();
        $aMonthAgo = new \DateTimeImmutable('a month ago');
        $user = User::factory()->create(['package_id' => 4]);
        $guid0 = get_guid('pod-');
        $guid1 = get_guid('pod-');
        $feed = Feed::factory()->create([
                'username' => $user->username,
                'domain' => [
                        'website_type' => 'wordpress',
                        'is_custom' => false,
                        'tld' => 'de',
                        'hostname' => 'https://123.podcaster.de',
                        'subdomain' => '123.podcaster',
                        'protocol' => 'https',
                ],
                'rss' => [
                    'title' => 'Test',
                    'description' => 'Beschreibung 123',
                    'link' => 'https://www.podcaster.de',
                    'author' => 'Max Mustermann',
                    'copyright' => 'Meine Rechte',
                ],
                'itunes' => [
                    'subtitle' => 'Untertitel',
                ],
                'entries' => [
                    0 => [
                        'title' => 'Episode 0',
                        'description' => 'Beschreibung zur Episode 0',
                        'date_created' => $now,
                        'author' => 'Autor 0',
                        'link' => 'https://link.to/0',
                        'guid' => $guid0,
                        'enclosure' => [
                            'url' => 'https://download.from/0',
                        ]
                    ],
                    1 => [
                        'title' => 'Episode 1',
                        'description' => 'Beschreibung zur Episode 1',
                        'date_created' => $aMonthAgo,
                        'author' => 'Autor 1',
                        'link' => 'https://link.to/1',
                        'guid' => $guid1,
                        'enclosure' => [[
                            'url' => 'https://download.from/1',
                        ]]
                    ],
                ]
            ],
        );
        $this->withoutEvents();
        $fgm = new FeedGeneratorManager($user->username, $feed->feed_id);
        $result = $fgm->publish();
        $feedPath = storage_path(get_user_feed_path($user->username)) . DIRECTORY_SEPARATOR . $feed->feed_id . '.rss';

        $importedFeed = Reader::importFile($feedPath);

        $this->assertEquals('Test', $importedFeed->getTitle());
        $this->assertEquals('Beschreibung 123', $importedFeed->getDescription());
        $this->assertEquals('https://www.podcaster.de', $importedFeed->getLink(0));
        $this->assertEquals('Max Mustermann', $importedFeed->getAuthor(0)['name']);
        $this->assertEquals('Meine Rechte', $importedFeed->getCopyright());

        $c = 1;
        foreach ($importedFeed as $key => $entry) {
            $this->assertEquals("Episode {$c}", $entry->getTitle());
            $this->assertEquals("Beschreibung zur Episode {$c}", $entry->getDescription());
            $this->assertEquals("Autor {$c}", $entry->getAuthor()['name']);
            $this->assertEquals("https://link.to/{$c}", $entry->getLink());
            $this->assertEquals("Beschreibung zur Episode {$c}", $entry->getContent());
            $this->assertEquals($c === 1 ? $guid1 : $guid0, $entry->getId());
            $this->assertEquals("https://link.to/{$c}", $entry->getPermalink());
            $this->assertEquals("https://download.from/{$c}", $entry->getEnclosure());

/*            if ($c == 1) {
                $this->assertEquals($aMonthAgo, $entry->getDateCreated());
            } else {
                $this->assertTrue($now->eq($entry->getDateCreated()));
            }
            $this->assertEquals("EXP", $entry->getDateModified());
            */
            --$c;

/*
                'dateModified' => $entry->getDateModified(),
                'authors'      => $entry->getAuthors(), */

        }
    }

    public function testBlogTitle()
    {
        $this->withoutEvents();
        $this->withoutNotifications();
        $user = User::factory()->create(['package_id' => 1]);

        $guid0 = get_guid('pod-');
        $guid1 = get_guid('pod-');
        $now = now();
        $feed = Feed::factory()->create([
            'username' => $user->username,
            'domain' => [
                'website_type' => 'wordpress',
                'is_custom' => false,
                'tld' => 'de',
                'hostname' => 'https://123.podcaster.de',
                'subdomain' => '123.podcaster',
                'protocol' => 'https',
            ],
            'rss' => [
                'title' => 'Test',
                'description' => 'Beschreibung 123',
                'link' => 'https://www.podcaster.de',
                'author' => 'Max Mustermann',
                'copyright' => 'Meine Rechte',
            ],
            'itunes' => [
                'subtitle' => 'Untertitel',
            ],
            'entries' => [
                0 => [
                    'title' => 'Episode 0',
                    'description' => 'Beschreibung zur Episode 0',
                    'date_created' => $now,
                    'author' => 'Autor 0',
                    'link' => 'https://link.to/0',
                    'guid' => $guid0,
                    'enclosure' => [
                        'url' => 'https://download.from/0',
                    ]
                ],
                1 => [
                    'title' => 'Episode 1',
                    'description' => 'Beschreibung zur Episode 1',
                    'date_created' => $now,
                    'author' => 'Autor 1',
                    'link' => 'https://link.to/1',
                    'guid' => $guid1,
                    'enclosure' => [[
                        'url' => 'https://download.from/1',
                    ]]
                ],
            ]
        ],
        );
        $fgm = new FeedGeneratorManager($user->username, $feed->feed_id);

        $title = $fgm->getBlogTitle('Das ist was Einfaches');
        $this->assertIsString($title);
        $this->assertEquals('das-ist-was-einfaches', $title);

        $title = $fgm->getBlogTitle('A');
        $this->assertIsString($title);
        $this->assertEquals('a', $title);

        $title = $fgm->getBlogTitle('A1');
        $this->assertIsString($title);
        $this->assertEquals('a1', $title);

        $title = $fgm->getBlogTitle('A 1');
        $this->assertIsString($title);
        $this->assertEquals('a-1', $title);

        $title = $fgm->getBlogTitle('A 1!');
        $this->assertIsString($title);
        $this->assertEquals('a-1', $title);

        $title = $fgm->getBlogTitle('A--1!');
        $this->assertIsString($title);
        $this->assertEquals('a-1', $title);

        $title = $fgm->getBlogTitle('#~$A-§-1=?!');
        $this->assertIsString($title);
        $this->assertEquals('a-1', $title);

        $title = $fgm->slugify('наклейки');
        $this->assertIsString($title);
        $this->assertEquals('naklejki', $title);
    }
}
