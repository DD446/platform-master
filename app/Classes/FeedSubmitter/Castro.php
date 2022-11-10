<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Castro extends FeedSubmitter
{
    protected $placeholderLink = 'https://castro.fm';

    public function submit()
    {
        return 'https://castro.fm/itunes/';
    }
}
