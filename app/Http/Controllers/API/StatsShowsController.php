<?php

namespace App\Http\Controllers\API;

use App\Classes\StatisticsElastic;
use App\Http\Controllers\Controller;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Str;

class StatsShowsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        $username = auth()->user()->username;
        $request = request();
        $validated = $this->validate($request, [
            'e' => ['nullable', 'required', 'string'],
            'i' => ['required', 'string', 'max:100', 'exists:App\Models\Feed,feed_id,username,' . $username],
            'df' => ['nullable', 'date'],
            'dt' => ['nullable', 'date'],
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
        ], [], []);

        if (!$validated['df']) {
            $df = now()->subMonth()->startOfDay()->format('Y-m-d H:m:s.v');
        } else {
            $df = $validated['df'];
        }

        if (!isset($validated['dt']) || !$validated['dt']) {
            $dt = now()->endOfDay()->format('Y-m-d H:m:s.v');
        } else {
            $dt = $validated['dt'];
        }

        if (\request()->has('df')) {
            $df = Carbon::createFromTimestamp(strtotime($df))->startOfDay()->addHour()->format('Y-m-d H:i:s.v');
        }

        if (\request()->has('dt')) {
            $dt = Carbon::createFromTimestamp(strtotime($dt))->endOfDay()->addHour()->format('Y-m-d H:i:s.v');
        }

        $range = ['df' => $df, 'dt' => $dt];
        $page = \request('page', ['size' => 5, 'number' => 1]);
        $feedId = $validated['i'] ?? null;
        $episodeId = $validated['e'] ?? null;
        $data = (new StatisticsElastic())->getShows($username, $range, $page, $feedId, $episodeId);

        return response()->json($data);
    }
}
