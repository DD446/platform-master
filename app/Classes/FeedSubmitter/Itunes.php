<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use Illuminate\Support\Facades\Http;
use App\Classes\FeedSubmitter;

class Itunes extends FeedSubmitter
{
    protected $canValidate = true;

    protected $placeholderLink = 'https://podcasts.apple.com/de/podcast/';

    protected $helpLink = 'https://www.podcaster.de/faq/antwort-21-wie-melde-ich-meinen-podcast-bei-itunes-an';

    public function submit()
    {
        return 'https://podcastsconnect.apple.com/my-podcasts/new-feed?submitfeed=' . $this->getUrl();
    }

    public function check()
    {
        if (parent::check()) {
            return true;
        }
        $api = 'https://itunes.apple.com/search?entity=podcast&limit=50&sort=recent&term=';
        $searchUrl = $api . $this->feed->rss['title'];

        $res = Http::get($searchUrl);

        if($res->status() < 400) {
            $content = $res->body();

            if ($content) {
                $oRes = json_decode($content);

                if ($oRes->resultCount === 0) {
                    return false;
                }

                foreach ($oRes->results as $result) {
                    if ($result->feedUrl == $this->getUrl()) {
                        $this->link = $result->collectionViewUrl;
                        $this->podcastLinkService->save($this->feed, 'itunes', $this->link);

                        return true;
                    }
                }
                // TODO: Try harder, next result set if there are more results
            }
        }

        return false;
    }
}
