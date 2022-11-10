<?php

namespace App\Http\Controllers\API;

use App\Models\Feed;
use App\Models\Show;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Classes\Statistics;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StatsCounterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $username = auth()->user()->username;
        $range = \request('range');
        $source = \request('s');
        $type = 'day'; // TODO

        switch (\request('stat')) {
            case 'subscribers':
                $count = (new Statistics())->subscribers($username, $range, $type, $source);
                break;
            case 'listeners':
                $count = (new Statistics())->listeners($username, $range, $type, $source);
                break;
            case 'downloads':
                $count = -1;
                break;
            default:
                throw new \Exception('Unknown param `' . \request('stat') . '` passed.');
        }

        return response()->json($count);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function topshows()
    {
        $username = auth()->user()->username;
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            's' => ['nullable', 'string', 'exists:App\Models\Feed,feed_id,username,' . $username],
        ]);

        $page = request('page', ['number' => 1, 'size'  => 10]);
        $start = strtotime($validated['df']);
        $end = strtotime($validated['dt']);
        $source = $validated['s'] ?? null;
        $cacheKey = 'STATS_TOPSHOWS_' . $username . '_' . $page['number'] . '_' . $page['size'] . '_' . $start . '_' . $end . '_' . Str::lower($source);

        if (Cache::has($cacheKey)) {
            $items = Cache::get($cacheKey);
        } else {
            $items = (new Statistics())->topshows($username, $page, $start, $end, $source);
            Cache::put($cacheKey, $items, now()->addHours(6));
        }

        return response()->json($items);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function lastshows()
    {
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            's' => ['nullable', 'string', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ]);

        $username = auth()->user()->username;
        $page = request('page', ['number' => 1, 'size'  => 10]);
        $start = strtotime($validated['df']);
        $end = strtotime($validated['dt']);
        $source = $validated['s'] ?? null;
        $cacheKey = 'STATS_LASTSHOWS_' . $username . '_' . $page['number'] . '_' . $page['size'] . '_' . $start . '_' . $end . '_' . Str::lower($source);

        if (Cache::has($cacheKey)) {
            $items = Cache::get($cacheKey);
        } else {
            $items = (new Statistics())->lastshows($username, $page, $start, $end, $source);
            Cache::put($cacheKey, $items, now()->addHours(6));
        }

        return response()->json($items);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function charts()
    {
        $username = auth()->user()->username;
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            's' => ['nullable', 'string', 'exists:App\Models\Feed,feed_id,username,' . $username],
        ]);

        $page = request('page', ['number' => 1, 'size'  => 10]);
        $source = $validated['s'] ?? null;
        $cacheKey = 'STATS_CHARTS_' . $username . '_' . $page['number'] . '_' . $page['size'] . '_' . Str::lower($source);

        if (Cache::has($cacheKey)) {
            $items = Cache::get($cacheKey);
        } else {
            $items = (new Statistics())->charts($username, $page, $source);
            Cache::put($cacheKey, $items, now()->addHours(6));
        }
        return response()->json($items);
    }
}
