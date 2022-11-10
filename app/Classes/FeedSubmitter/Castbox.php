<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Castbox extends FeedSubmitter
{
    protected $placeholderLink = 'https://castbox.fm/channel/id';

    protected $helpLink = 'https://helpcenter.castbox.fm/portal/en/kb/articles/submit-my-podcast';

    public function submit()
    {
        return 'https://castbox.fm/creator/channels';
    }
}
