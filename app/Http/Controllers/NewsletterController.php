<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    //
    public function getEbook()
    {
        $signUpUrl = 'https://www.podcast.de/mailcoach/subscribe/123e1547-b35d-499b-97f1-a539e4cfbffc';
        $response = Http::post($signUpUrl, ['email' => \request('email'), 'tags' => \request('tags')]);

        return $response->ok();
    }

    public function getRecordingChecklist()
    {
        $signUpUrl = 'https://www.podcast.de/mailcoach/subscribe/5fe214fe-42f4-4bfc-8f0a-3e773da77879';
        $response = Http::post($signUpUrl, ['email' => \request('email'), 'tags' => \request('tags')]);

        return $response->ok();
    }

    public function getPodcastConcept()
    {
        $signUpUrl = 'https://www.podcast.de/mailcoach/subscribe/5fe214fe-42f4-4bfc-8f0a-3e773da77879';
        $response = Http::post($signUpUrl, ['email' => \request('email'), 'tags' => \request('tags')]);

        return $response->ok();
    }
}
