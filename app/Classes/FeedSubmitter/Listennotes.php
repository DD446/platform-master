<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Listennotes extends FeedSubmitter
{
    protected $placeholderLink = 'https://www.listennotes.com/podcasts/';

    public function submit()
    {
        return 'https://www.listennotes.com/de/submit/';
    }
}
