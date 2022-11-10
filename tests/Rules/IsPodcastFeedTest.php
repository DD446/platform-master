<?php
/**
 * User: fabio
 * Date: 02.11.21
 * Time: 10:10
 */

namespace Tests\Rules;

use App\Rules\IsPodcastFeed;
use Tests\TestCase;

class IsPodcastFeedTest extends TestCase
{

    /**
     * @var IsPodcastFeed
     */
    protected $rule;

    protected $feedUrls = [
        "https://podcast-plattform.podcaster.de/podcastde-news.rss",
        "https://predigtfeg.podspot.de/rss",
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new IsPodcastFeed();
    }

    public function testUrlPass()
    {
        foreach ($this->feedUrls as $url) {
            $this->assertTrue($this->rule->passes('url', $url));
        }
    }

    public function testActiveUrlPass()
    {
        foreach ($this->feedUrls as $url) {
            $this->assertTrue($this->rule->passes('active_url', $url));
        }
    }

    public function testUrlPassFail()
    {
        $this->assertFalse($this->rule->passes('url', "https://www.podcastplattform.de/"));
    }

    public function testActiveUrlPassFail()
    {
        $this->assertFalse($this->rule->passes('active_url', "https://ich.habemir-das-nur-ausgedacht123.de/"));
    }
}
