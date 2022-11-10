<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:36
 */

namespace App\Classes;


use Buzz\Browser;
use Buzz\Client\Curl;
use App\Classes\FeedSubmitter\Podcast;
use App\Models\Feed;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

abstract class FeedSubmitter
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @var bool
     */
    protected $canValidate = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $isForm = false;

    /**
     * @var string
     */
    protected $placeholderLink = '';

    /**
     * @var PodcastLinkService
     */
    protected $podcastLinkService;

    /**
     * @var string
     */
    protected $helpLink = '';

    /**
     * FeedSubmitter constructor.
     * @param  Feed  $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
        $this->url = get_feed_uri($feed->feed_id, $feed->domain);
        $this->type = strtolower((new \ReflectionClass($this))->getShortName());
        $this->podcastLinkService = new PodcastLinkService();
    }

    public function check()
    {
        if (!$this->link) {
            $link = $this->getLinkFromStorage();

            if (!empty($link)) {
                $this->setLink($link);
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isForm(): bool
    {
        return $this->isForm;
    }

    protected function getUrl()
    {
        return $this->url;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    protected function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return bool
     */
    public function canValidate(): bool
    {
        return $this->canValidate;
    }

    /**
     * @return string
     */
    private function getLinkFromStorage(): string
    {
        $aLinks = $this->feed->submit_links ?? [];

        if (array_key_exists($this->type, $aLinks)) {
            return $aLinks[$this->type];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPlaceholderLink(): ?string
    {
        return $this->placeholderLink;
    }

    /**
     * @return string
     */
    public function getHelpLink(): ?string
    {
        return $this->helpLink;
    }
}
