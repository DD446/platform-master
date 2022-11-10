<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Fyyd extends FeedSubmitter
{
    protected $placeholderLink = 'https://fyyd.de/podcast/';

    protected $helpLink = 'https://fyyd.de/fragen';

    protected $isForm = true;

    public function submit()
    {
        //return 'https://fyyd.de/add-feed';
        return '
<form method="post" action="https://fyyd.de/add-feed" target="_blank">
    <input type="hidden" name="newfeed" value="' .  get_feed_uri($this->feed->feed_id, $this->feed->domain) . '">
    <input type="hidden" name="knorx" value="">
    <button type="submit" class="btn btn-primary btn-lg">' . trans('feeds.link_submit_podcast') .'</button>
</form>
        ';
    }
}
