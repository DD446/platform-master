<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Podimo extends FeedSubmitter
{
    protected $placeholderLink = 'https://podimo.com/shows/';

    //protected $helpLink = '';

    public function submit()
    {
        return 'https://podimo.com/de/podcasters';
    }
}
