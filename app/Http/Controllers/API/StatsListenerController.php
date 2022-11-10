<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StatsListenerRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feed;

class StatsListenerController extends Controller
{
    /**
     * Listeners
     *
     * Fetch listener data of your podcasts.
     *
     * @group Statistics
     *
     * @param  StatsListenerRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     * @authenticated
     * @hideFromAPIDocumentation
     */
    public function index(StatsListenerRequest $request)
    {
        $validated = $request->validated();

        $dateFrom = request('date_from', now()->subMonth());
        $dateTo = request('date_to', now());
        $timeResolution = request('time_resolution', 'day');
        $userAgentType = request('user_agent_type', ['apps', 'browsers', 'desktop']);
        $type = $validated['type'];
        $feedId = $validated['feed_id'];
        $guid = $validated['guid'];
        $mediaId = $validated['media_id'];
        $userAgent = $validated['user_agent'];
        $os = $validated['os'];
        $country = $validated['country'];
        $referer = $validated['referer'];

        $requests = \App\Models\Request::whereIn('user_agent_type', $userAgentType)
            ->where('type', $timeResolution)
            /*->where('date', '=', $yesterdayFormatted)*/
            ->whereRaw(['user_agent' => ['$exists' => false]], [], 'and')
            ->whereRaw(['operating_system' => ['$exists' => false]], [], 'and')
            ->whereRaw(['geo' => ['$exists' => false]], [], 'and')
            ->whereRaw(['referer' => ['$exists' => false]], [], 'and')
            ->get();

        return response()->json($requests);
    }
}
