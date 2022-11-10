<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Playerfm extends FeedSubmitter
{
    protected $placeholderLink = 'https://player.fm/series/';

    public function submit()
    {
        return 'https://player.fm/suggest';
    }
}
