<?php

namespace App\Http\Controllers;

use App\Classes\MediaManager;
use App\Events\FeedUpdateEvent;
use App\Events\LogoSavedEvent;
use App\Models\Feed;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Classes\FeedWriterLegacy;
use App\Rules\LogoHasNoTransparency;
use App\Rules\StorageSpaceAvailable;
use Illuminate\Support\Facades\File;

class FeedLogoController extends MediaController
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => ['image', 'mimes:jpeg,png', 'required', 'max:1512', 'dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1.0', new StorageSpaceAvailable, new LogoHasNoTransparency],
            'feed' => 'string|nullable',
        ], [], [
            'file' => 'Logo',
        ]);

        $mediaManager = new MediaManager(auth()->user());
        $aRes = $mediaManager->saveFile($request->file('file'));

        $feedId = \request('feed');

        if ($feedId) {
            // Set image as feed logo
            $username = auth()->user()->username;

            if (Feed::setLogo($username, $feedId, $aRes['id'])) {
                event(new FeedUpdateEvent($username, $feedId));
            }
            event(new LogoSavedEvent(auth()->user(), $aRes['file'], $feedId));
        }
        unset($aRes['file']['path']);

        return response()->json($aRes)->setStatusCode($aRes['statusCode'], $aRes['statusText']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($feed)
    {
        //$feedWriter = new FeedWriterLegacy();
        //$feedWriter->setLogo(auth()->user()->username, $feed);
        // TODO
        // Delete logo from rss / iTunes
        $username = auth()->user()->username;

        if (Feed::deleteLogo($username, $feed)) {
            event(new FeedUpdateEvent($username, $feed));
        }

        return response()->json(trans('feeds.success_logo_removed'));
    }

    public function check()
    {
        $file = request('file');
        $name = request('name');
        $mm = new MediaManager(auth()->user());

        // File comes from url
        if (!$name) {
            $res = $mm->saveFileFromUrl($file);
        } else {
            // TODO
            $res['statusCode'] = 500;
            $res['message'] = 'Not implemented';
        }

        if ($res['statusCode'] !== 200) {
            throw new \Exception($res['message']);
        }

        $file = $res['file'];

        return is_logo($file, true);
    }
}
