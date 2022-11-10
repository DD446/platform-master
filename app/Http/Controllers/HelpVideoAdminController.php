<?php

namespace App\Http\Controllers;

use App\Models\HelpVideo;
use App\Models\User;
use Illuminate\Http\Request;

class HelpVideoAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        if (!in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            abort(409);
        }

        $videos = HelpVideo::all();

        return view('pages.help.video.admin.index', compact('videos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HelpVideo  $helpVideo
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(HelpVideo $video)
    {
        $user = User::whereUsername($video->username)->first();
        $mp4s = $user->getFiles('name', false, '.mp4')['items'];
        $webms = $user->getFiles('name', false, '.webm')['items'];
        $ogvs = $user->getFiles('name', false, '.ogv')['items'];
        $posters = $user->getFiles('name', false, 'type:image')['items'];

        return view('pages.help.video.admin.edit', compact('video', 'mp4s', 'webms', 'ogvs', 'posters'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HelpVideo  $helpVideo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, HelpVideo $video)
    {
        $validated = $this->validate($request, [
            'mp4' => ['nullable', 'numeric'],
            'webm' => ['nullable', 'numeric'],
            'ogv' => ['nullable', 'numeric'],
            'poster' => ['nullable', 'numeric']
        ]);

        if (!$video->update($validated)) {
            return redirect()->back()->with(['error' => 'Der Eintrag konnte nicht aktualisiert werden.']);
        }

        return redirect()->back()->with(['success' => 'Du hast den Eintrag erfolgreich aktualisiert.']);
    }
}
