<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Audionow extends FeedSubmitter
{
    protected $placeholderLink = 'https://audionow.de/podcast/';

    //protected $helpLink = 'https://www.podcaster.de/faq/antwort-';

    public function submit()
    {
        return 'https://audionow.de/';
    }
}
