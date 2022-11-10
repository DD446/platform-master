<?php
/**
 * User: fabio
 * Date: 27.05.19
 * Time: 21:35
 */

namespace App\Classes\FeedSubmitter;

use Illuminate\Support\Facades\Http;
use App\Classes\FeedSubmitter;

class Podcast extends FeedSubmitter
{
    protected $canValidate = true;

    protected $placeholderLink = 'https://www.podcast.de/podcast/';

    protected $helpLink = 'https://www.podcast.de/faq/antwort-45-Wo+melde+ich+meinen+Podcast+bei+podcast.de+an%3F/';

    public function submit()
    {
        return 'https://www.podcast.de/podcast-anmelden/?url=' . rawurlencode($this->getUrl());
    }

    public function check()
    {
        if (parent::check()) {
            return true;
        }
        $baseUrl = 'https://www.podcast.de/podcast/';
        $api = 'https://www.podcast.de/api/ping?url=';
        $searchUrl = $api . rawurlencode($this->getUrl());

        // Attention: Do NOT change to HEAD request
        // because content of response from GET request is used
        $res = Http::post($searchUrl);

        if($res->status() === 200) {
            $content = $res->body();

            if ($content) {
                $oRes = json_decode($content);

                if (!$oRes || !$oRes->channel_id) {
                    return false;
                }

                $this->link = $baseUrl . $oRes->channel_id . '/';

                $this->podcastLinkService->save($this->feed, 'podcast', $this->link);

                return true;
            }
        }

        return false;
    }
}
