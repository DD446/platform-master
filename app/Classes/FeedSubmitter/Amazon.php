<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Amazon extends FeedSubmitter
{
    protected $placeholderLink = 'https://music.amazon.de/podcasts/';

    //protected $helpLink = '';

    public function submit()
    {
        return route('amazon.index');
    }
}
