<?php
/**
 * User: fabio
 * Date: 18.09.20
 * Time: 16:49
 */

namespace Tests\Unit;

use App\Classes\ShowManager;
use App\Models\Feed;
use Database\Factories\FeedFactory;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testGetGuid()
    {
        $guid1 = get_guid();
        $guid2 = get_guid();

        $this->assertNotEquals($guid1, $guid2);

        $guid3 = get_guid('pod');
        $this->assertStringContainsString('pod', $guid3);
    }

    public function testGetTlds()
    {
        $a = get_tlds();

        $this->assertIsArray($a);
        $this->assertGreaterThan(1500, count($a));
        $this->assertArrayHasKey('com', array_flip($a));
        $this->assertArrayHasKey('de', array_flip($a));
    }

    public function testChangePrefix()
    {
        $this->assertEquals(-1, change_prefix(1));
        $this->assertEquals(1, change_prefix(-1));
        $this->assertEquals(0, change_prefix(0));
    }

    public function testLinkShowForFeed()
    {
        $username = 'test';
        $feed = Feed::factory()->make();
        $show = get_newest_show($feed);

        link_show_for_feed($username, $feed, $show['guid']);


    }


}
