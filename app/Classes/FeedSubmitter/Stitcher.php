<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Stitcher extends FeedSubmitter
{
    protected $placeholderLink = 'https://www.stitcher.com/podcast/';

    public function submit()
    {
        return 'https://partners.stitcher.com/join';
    }
}
