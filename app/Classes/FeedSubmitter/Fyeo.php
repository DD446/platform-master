<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Fyeo extends FeedSubmitter
{
    protected $placeholderLink = 'https://www.fyeo.de/originals/';

    //protected $helpLink = 'https://www.podcaster.de/faq/antwort-';

    public function submit()
    {
        return 'https://www.fyeo.de/';
    }
}
