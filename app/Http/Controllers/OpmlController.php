<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feed;

class OpmlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $feed = Feed::owner()->findOrFail($id);

        $xmlUrl = get_feed_uri($feed->feed_id, $feed->domain);
        $htmlUrl = get_blog_uri($feed->domain);

/*        $html = view('feeds.opml.show', compact('feed', 'xmlUrl', 'htmlUrl'))->render();

        $headers = [
            'Content-Type' => 'application/xml',
        ];*/

        //return response()->download($html, $feed->feed_id . '.opml', $headers, 'attachment');

        return response(view('feeds.opml.show')->with(compact('feed', 'xmlUrl', 'htmlUrl')), 200, [
            'Content-Type' => 'application/xml', // use your required mime type
            'Content-Disposition' => 'attachment; filename="' . $feed->feed_id . '.opml"',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function edit(Feed $feed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feed $feed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
        //
    }
}
