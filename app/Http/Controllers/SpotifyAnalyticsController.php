<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SpotifyAnalytic;
use Illuminate\Http\Request;
use App\Models\SpotifyAnalyticsExport;

class SpotifyAnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('spotify.stats');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(Request $request)
    {
        $files = SpotifyAnalytic::select('show_title')->groupBy('show_title')->get();
        //$files = SpotifyAnalytic::owner()->select(['id', 'show_title'])->orderBy(DB::raw('CAST(show_title AS SIGNED), show_title'))->get();

        return response()->json($files);
    }

    public function first()
    {
        $date = SpotifyAnalytic::min('date');

        if (!$date) {
            $date = '2018-04-10';
        }

        $date = strtotime($date);

        return response()->json($date);
    }

    public function export(Request $request)
    {

    }
}
