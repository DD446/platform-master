<?php
/**
 * User: fabio
 * Date: 07.05.20
 * Time: 11:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Google extends FeedSubmitter
{
    protected $placeholderLink = 'https://podcasts.google.com/feed/';

    protected $helpLink = 'https://www.podcaster.de/faq/antwort-56-wie-melde-ich-meinen-podcast-beim-google-play-music-podcast-portal-fur-die-google-podcasts-app-an';

    public function submit()
    {
        return 'https://podcastsmanager.google.com/add-feed?hl=de';
    }
}
