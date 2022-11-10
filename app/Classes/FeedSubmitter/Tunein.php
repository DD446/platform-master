<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Tunein extends FeedSubmitter
{
    protected $placeholderLink = 'https://tunein.com/podcasts/Podcasts/';

    protected $helpLink = 'https://www.podcaster.de/faq/antwort-57-wie-melde-ich-meinen-podcast-bei-tunein-an';

    public function submit()
    {
        return 'https://help.tunein.com/contact/add-podcast-S19TR3Sdf';
    }
}
