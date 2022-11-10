<?php

namespace App\Http\Controllers;

use App\Classes\PodcastLinkService;
use App\Http\Requests\DeezerStoreRequest;
use App\Models\Feed;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Str;

class DeezerController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $this->seo()
            ->setTitle(trans('deezer.page_title'));

        if (request()->wantsJson()) {
            $feeds = Feed::owner()
                ->select(['feed_id', 'rss.title', 'domain.feed_redirect', 'settings.deezer'])
                ->get();
            $aFeeds = [];

            foreach ($feeds as $feed) {
                // User cannot submit redirected feeds to Spotify
                if ($feed['domain']['feed_redirect'] === true) {
                    continue;
                }
                $aFeeds[] = [
                    'id' => $feed['feed_id'],
                    'name' => $feed['rss']['title'],
                    'deezer' => $feed['settings']['deezer'] ?? [],
                ];
            }

            return response()->json($aFeeds);
        }

        return view('deezer.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(DeezerStoreRequest $request)
    {
        $validated = $request->validated();
        $feed = Feed::owner()->findOrFail($validated['feed_id']);

        $url = 'https://podcasters.deezer.com/api/podcast/submission';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'api_key' => config('services.deezer.api_key'),
        ])->post($url, [
            "title" => $feed->rss['title'],
            "url" => get_feed_uri($feed->feed_id, $feed->domain),
            "provider" => config('services.deezer.provider'),
            "country" => auth()->user()->country ?? 'DE',
            "genre" => $feed->itunes['category'][0] ?? Feed::ITUNES_CATEGORY_DEFAULT,
            "language" => $feed->rss['language'] ?? 'de', // TODO: I18N
            "explicitStatus" => (bool)$feed->itunes['explicit'],
        ]);

        if (!$response->ok()) {
            throw new \Exception(trans('deezer.error_submission', ['message' => $response->serverError()]));
        }

        $deezerUrl = $response->json('url');

        if (!$deezerUrl) {
            throw new \Exception(trans('deezer.error_submission_missing_url'));
        }

        $settings = $feed->settings;
        $settings['deezer'] = [
            'date' => new \MongoDB\BSON\UTCDateTime(now()),
            'coo' => \request('coo'),
            'id' => Str::after($deezerUrl, 'https://www.deezer.com/de/show/'),
        ];
        (new PodcastLinkService())->save($feed, 'deezer', $deezerUrl);

        $res = ['message' => trans('deezer.success_podcast_added', ['url' => $deezerUrl])];
        $code = 200;

        if (!$feed->whereUsername(auth()->user()->username)->whereFeedId($feed->feed_id)->update(['settings' => $settings])) {
            $res = ['error' => trans('deezer.message_saving_submission_failed')];
            $code = 500;
        }

        return response()->json($res)->setStatusCode($code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function destroy($id)
    {
        $feed = Feed::owner()->findOrFail($id);

        $url = 'https://podcasters.deezer.com/api/podcast/disable';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'api_key' => config('services.deezer.api_key'),
        ])->post($url, [
            "url" => get_feed_uri($feed->feed_id, $feed->domain),
        ]);

        if (!$response->ok()) {
            throw new \Exception(trans('deezer.error_disabling', ['message' => $response->clientError()]));
        }

        $message = $response->json('message');

        $settings['deezer'] = [];
        $res = ['message' => $message];
        $code = 200;

        if (!$feed->whereUsername(auth()->user()->username)->whereFeedId($id)->update(['settings' => $settings])) {
            $res = ['error' => trans('deezer.message_saving_submission_failed')];
            $code = 500;
        }

        return response()->json($res)->setStatusCode($code);
    }
}
