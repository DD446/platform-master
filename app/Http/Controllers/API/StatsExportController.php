<?php

namespace App\Http\Controllers\API;

use App\Classes\Statistics;
use App\Http\Controllers\Controller;
use App\Models\StatsExport;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StatsExportController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        $exports = StatsExport::owner()->take(StatsExport::LATEST_COUNT)->latest()->get();

        return response()->json(['exports' => $exports, 'availableExports' => get_package_feature_statistics_export(auth()->user()->package, auth()->user())]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function store(Request $request)
    {
        Gate::inspect('create');

        if (\request('feed_id') === Statistics::ALL_FEEDS) {
            $request->request->set('feed_id', null);
        }

        if (\request('show_id') === Statistics::ALL_SHOWS) {
            $request->request->set('show_id', null);
        }

        $validated = $this->validate($request, [
            'start' => 'required_if:date,custom|date',
            'end' => 'required_if:date,custom|date',
            'date' => 'nullable|in:custom,all',
            'feed_id' => ['nullable', 'string', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
            'show_id' => 'nullable|string',
            'sort_order' => 'string|in:asc,desc',
            'sort_by' => 'string|in:hits,date',
            'restrict' => 'nullable|string|in:newest',
            'restrict_limit' => 'nullable|numeric|max:1000',
            'limit' => 'nullable|numeric|max:1000',
            'offset' => 'nullable|numeric|max:1000',
        ], [], [
            'start' => trans('stats.valdidation_error_statistics_start_date'),
            'end' => trans('stats.valdidation_error_statistics_end_date'),
            'feed_id' => trans('stats.valdidation_error_statistics_feed'),
            'show_id' => trans('stats.valdidation_error_statistics_show'),
            'limit' => trans('stats.validation_error_statistics_limit'),
        ]);


        if (Str::lower($validated['date']) != 'all') {
            $start = CarbonImmutable::createFromTimestamp(strtotime($validated['start']))->startOfDay();
            $end = CarbonImmutable::createFromTimestamp(strtotime($validated['end']))->endOfDay();
        } else {
            unset($validated['date']);
            $start = $end = null;
        }

        $se = new StatsExport($validated);
        $se->user_id = auth()->id();
        $se->start = $start;
        $se->end = $end;

        if (!$se->save()) {
            throw new \Exception(trans('stats.error_save_export'));
        }

        return response()->json([
            'message' => trans('stats.success_save_export'),
            'id' => $se->id,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @hideFromAPIDocumentation
     */
    public function csv()
    {
        //return view('stats.export.index');

        if (auth()->user()->package_id < 4) {
            abort(404);
        }

        $fileName = auth()->user()->username . '.csv';
        /*        $headers = [
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=" . $fileName,
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                ];*/

        $content = null;
        //$yesterday = CarbonImmutable::yesterday()/*->format('Y-m-d')*/;

        // podcasterDe-Statistiken_Abonnenten_Benutzer-hoerspiele_2019-04-22-2019-05-21
        /*        $date = Carbon::now()->subMonth()->format('Y-m-d');
                $content = ExternalSubscriber::where('date', '>=', $date)->orderBy('created', 'ASC')->get(['date', 'user_agent', 'subscribers']);*/

        // podcasterDe-Statistiken_Hoerer_Benutzer-hoerspiele_2019-05-21.csv
        $date = Carbon::now()->subWeek()->format('d-M-Y');
        $content = \App\Models\Request::where('date', '>=', $date)
            ->where('type', '=', 'day')
            ->where('user_agent_type', 'exists', true)
            ->where('user_agent', 'exists', true)
            /*

                        ->where('operating_system', 'exists', true)
                        ->where('geo', 'exists', true)
                        ->where('referer', 'exists', true)*/
            /*            ->whereRaw(['user_agent_type' => ['$exists' => true]], [], 'and')
                        ->whereRaw(['user_agent' => ['$exists' => true]], [], 'and')
                        ->whereRaw(['operating_system' => ['$exists' => true]], [], 'and')
                        ->whereRaw(['geo' => ['$exists' => true]], [], 'and')
                        ->whereRaw(['referer' => ['$exists' => true]])*/
            ->get(['date', 'feed_id', 'media', 'hits', 'bytes', 'user_agent_type', 'user_agent'/*, 'operating_system', 'referer', 'geo.country_name'*/]);


        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($content) {
                $csvExporter = new \Laracsv\Export();
                $filehandler = fopen('php://output', 'r');
                fwrite($filehandler, $csvExporter->build($content, ['date', 'feed_id', 'media', 'hits', 'bytes', 'user_agent_type', 'user_agent', 'operating_system', 'referer', 'geo.country_name'])->getWriter()->getContent());
                fclose($filehandler);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
    }

    /**
     * @param  StatsExport  $export
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @hideFromAPIDocumentation
     */
    public function show(StatsExport $export)
    {
        //$export = $id;
        //$export = StatsExport::findOrFail($id);
        $export->increment('downloads');

        return response()->download(storage_path('/statistics/export/' . $export->user_id . DIRECTORY_SEPARATOR . $export->id), $export->filename . '.' . $export->format);
    }

    /**
     * @param  StatsExport  $export
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @hideFromAPIDocumentation
     */
    public function destroy(StatsExport $export)
    {
        if (!$export->delete()) {
            throw new \Exception(trans('stats.error_delete_export'));
        }

        return response()->json([
            'message' => trans('stats.success_delete_export'),
        ]);
    }

    /**
     * @param  StatsExport  $export
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function update(StatsExport $export)
    {
        $export->increment('downloads');

        return response()->json($export->downloads);
    }
}
