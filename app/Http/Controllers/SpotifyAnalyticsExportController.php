<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\SpotifyAnalyticsExport;
use Illuminate\Http\Request;

class SpotifyAnalyticsExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        $spae = SpotifyAnalyticsExport::select(['id', 'show_title', 'start', 'end', 'is_exported'])->get();

        if (!$spae) {
            throw new \Exception(trans('spotify.error_get_exports'));
        }

        return response()->json($spae);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date',
            'show_title' => 'nullable|exists:mongodb.spotify_analytics,show_title',
            'data_type' => 'in:raw',
        ], [], [
            'start' => trans('spotify.valdidation_error_statistics_start_date'),
            'end' => trans('spotify.valdidation_error_statistics_end_date'),
            'show_title' => trans('spotify.valdidation_error_statistics_show_title'),
            'data_type' => trans('spotify.valdidation_error_statistics_data_type'),
        ]);

        $spae = new SpotifyAnalyticsExport();
        $spae->user_id = auth()->user()->id;
        $spae->start = date('Y-m-d', strtotime($request->start));
        $spae->end = date('Y-m-d', strtotime($request->end));
        $spae->show_title = $request->input('show_title');
        $spae->data_type = $request->input('data_type');

        if (!$spae->save()) {
            throw new \Exception(trans('spotify.error_save_export'));
        }

        return response()->json([
            'message' => trans('spotify.success_save_export'),
            'id' => $spae->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $spotifyAnalyticsExport = SpotifyAnalyticsExport::findOrFail($id);
        $spotifyAnalyticsExport->increment('download_counter');

        $path = SpotifyAnalyticsExport::getPath($spotifyAnalyticsExport);
        $file = $path . $spotifyAnalyticsExport->id . SpotifyAnalyticsExport::DEFAULT_EXTENSION;
        $archiveName = 'podcaster_statistiken_rohdaten-export_spotify_' // @TODO: I18N
            . ($spotifyAnalyticsExport->show_title ? Str::slug($spotifyAnalyticsExport->show_title) : 'alle-episoden') // @TODO: I18N
            . '_'
            . \date('Y-m-d', strtotime($spotifyAnalyticsExport->start))
            . '-'
            . \date('Y-m-d', strtotime($spotifyAnalyticsExport->end))
            . SpotifyAnalyticsExport::DEFAULT_EXTENSION;

        return response()->download($file, $archiveName);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SpotifyAnalyticsExport  $spotifyAnalyticsExport
     * @return \Illuminate\Http\Response
     */
    public function edit(SpotifyAnalyticsExport $spotifyAnalyticsExport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SpotifyAnalyticsExport  $spotifyAnalyticsExport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SpotifyAnalyticsExport $spotifyAnalyticsExport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SpotifyAnalyticsExport  $spotifyAnalyticsExport
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $spotifyAnalyticsExport = SpotifyAnalyticsExport::findOrFail($id);

        if (!$spotifyAnalyticsExport->delete()) {
            throw new \Exception(trans('spotify.error_delete_export'));
        }

        return response()->json([
            'message' => trans('spotify.success_delete_export'),
        ]);
    }
}
