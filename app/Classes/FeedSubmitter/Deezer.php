<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;
use App\Models\Feed;
use Illuminate\Support\Facades\Http;

class Deezer extends FeedSubmitter
{
    protected $isForm = false;

    protected $placeholderLink = 'https://www.deezer.com/de/show/';

    protected $helpLink = 'https://www.podcaster.de/faq/antwort-51-wie-melde-ich-meinen-podcast-bei-deezer-an';

    public function submit()
    {
        return route('deezer.index');

        //return 'https://podcasters.deezer.com/submission';

/*        $explicit = 2;
        switch (strtolower($this->feed->itunes['explicit'])) {
            case false :
            case 0 :
            case '0' :
            case 'no' :
                $explicit = 0;
                break;
            case true :
            case 1 :
            case '1' :
            case 'yes' :
                $explicit = 1;
                break;
            default :
                $explicit = 2;
        }

        $country = strtoupper(explode('-', $this->feed->rss['language'])[0]);

        //return 'https://lnk.to/submitmypodcast';
        return '
<form action="https://podcasters.deezer.com/submission" method="post" target="_blank">
        <input type="hidden" name="providerName" value="' . $this->feed->rss['author'] . '">
        <input type="hidden" name="providerEmail" value="' . $this->feed->rss['email'] . '">
        <input type="hidden" name="podcastTitle" value="' . $this->feed->rss['title'] . '">
        <input type="hidden" name="podcastUrl" value="' .  get_feed_uri($this->feed->feed_id, $this->feed->domain) . '">
        <input type="hidden" name="podcastGenre" value="">
        <input type="hidden" name="podcastCountry" value="' . $country . '">
        <input type="hidden" name="podcastExplicitStatus" value="' . $explicit . '">
        <!-- 0 = no, 1 = yes, 2 = unknown -->
        <input type="hidden" name="termsOfUse" value="1">
        <button type="submit" class="btn btn-primary btn-lg">' . trans('feeds.link_submit_podcast') .'</button>
</form>';*/
    }
}
