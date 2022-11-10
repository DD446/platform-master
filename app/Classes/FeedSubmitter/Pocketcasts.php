<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Pocketcasts extends FeedSubmitter
{
    protected $placeholderLink = 'https://pca.st/';

    protected $helpLink = 'https://support.pocketcasts.com/article/submitting-podcasts/';

    public function submit()
    {
        return 'https://pocketcasts.com/submit/';
    }
}
