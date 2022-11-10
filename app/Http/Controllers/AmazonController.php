<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Feed;

class AmazonController extends Controller
{
    const AMAZON_ACCESS_TOKEN = 'qWgyKarXqw8BNGk5fVpfxkY0FMlGgXenhMDlCkJ7UjK53178B8xhRqkHsBZUXSc4';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if (request()->wantsJson()) {
            $feeds = Feed::owner()
                ->select(['feed_id', 'rss.title', 'domain.feed_redirect', 'settings.amazon'])
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
                    'amazon' => $feed['settings']['amazon'] ?? [],
                ];
            }

            return response()->json($aFeeds);
        }

        \SEO::setTitle(trans('amazon.page_title'));

        return view('amazon.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'toc' => 'required|in:yes',
            'coo' => 'required|exists:countries,iso_3166_2',
        ], [
            //TODO: 'toc.in' => trans('amazon.validation_key_toc_yes'),
        ], [
            'id' => trans('amazon.validation_attribute_id'),
            'toc' => trans('amazon.validation_attribute_toc'),
            'coo' => trans('amazon.validation_attribute_coo')
        ]);

        $id = $request->get('id');
        $feed = Feed::owner()
            ->where('feed_id', '=', $id)
            ->firstOrFail();
        $settings = $feed->settings;
        $res = ['message' => trans('amazon.message_submission_saved')];
        $code = 200;

        $settings['amazon'] = [
            'date' => new \MongoDB\BSON\UTCDateTime(now()),
            'coo' => \request('coo'),
        ];

        if (!$feed->whereUsername(auth()->user()->username)->whereFeedId($id)->update(['settings' => $settings])) {
            $res = ['error' => trans('amazon.message_submission_failed')];
            $code = 500;
        }

        return response()->json($res)->setStatusCode($code);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        \SEO::setTitle(trans('amazon.page_title_terms'));

        // Used to display the Amazon ToC
        return view('amazon.show', ['hideNav' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function destroy($id)
    {
        $feed = Feed::owner()
            ->where('feed_id', '=', $id)
            ->firstOrFail();
        $settings = $feed->settings;
        $res = ['message' => trans('amazon.message_submission_withdrawn')];
        $code = 200;
        unset($settings['amazon']);

        if (!$feed->whereUsername(auth()->user()->username)->whereFeedId($id)->update(['settings' => $settings])) {
            $res = ['error' => trans('amazon.message_submission_withdrawal_failed')];
            $code = 500;
        }

        return response()->json($res)->setStatusCode($code);
    }

    public function fetch()
    {
        if (!\request()->has('token')
            || !\request()->filled('token')
            || !\request()->has('last')
            || !\request()->filled('last')) {
            abort(422);
        }

        $token = \request('token');

        if ($token !== self::AMAZON_ACCESS_TOKEN) {
            abort(403);
        }

        $last = \request('last');
        $time = new \MongoDB\BSON\UTCDateTime($last*1000);

        $results = Feed::where('settings.amazon', 'exists', true)
            ->whereRaw(['settings.amazon.date' => ['$gte' => $time]])
            ->where('settings.protection_id', '!=', 1)
            ->get(['feed_id', 'domain', 'rss', 'settings'])
            ->map(function($item) {
                return [
                    'title' => $item->rss['title'],
                    'feedUrl' => get_feed_uri($item->feed_id, $item->domain),
                    'countryOfOrigin' => $item->settings['amazon']['coo'],
                    //'date' => $item->settings['amazon'],
                ];
            });

        $ret = [
            'count' => $results->count(),
            'shows' => $results
        ];

        return response()->json($ret);
    }
}
