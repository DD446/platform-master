<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use App\Classes\FeedSubmitter;

class Spotify extends FeedSubmitter
{
    protected $canValidate = true;

    protected $placeholderLink = 'https://open.spotify.com/show/';

    protected $helpLink = 'https://www.podcaster.de/faq/antwort-52-wie-melde-ich-meinen-podcast-bei-spotify-an';

    public function submit()
    {
        return route('spotify.index');
    }

    public function check()
    {
        if (parent::check()) {
            return true;
        }
        $baseUrl = 'https://open.spotify.com/show/';

        if (isset($this->feed->settings['spotify'])
            && $this->feed->settings['spotify'] == 1
            && isset($this->feed->settings['spotify_uri'])
            && !empty($this->feed->settings['spotify_uri'])) {
            $a = explode(':', $this->feed->settings['spotify_uri']);
            $spotifyUri = array_pop($a);
            $this->link = $baseUrl . $spotifyUri;

            return true;
        }

        return false;
    }
}
