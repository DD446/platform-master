<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowTemplateStoreRequest;
use App\Models\ShowTemplate;
use Illuminate\Http\Request;

class ShowTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validated = \request()->validate([
            'feed_id' => ['required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ]);
        $feedId = $validated['feed_id'];

        ShowTemplate::owner()->where(function ($query) use ($feedId) {
            return $query->whereNull('feed_id')
                ->orWhere('feed_id', '=', $feedId);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ShowTemplateStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['itunes_episode_type'] = $validated['itunes_episode_type'] ?? 'full';

        $showTemplate = ShowTemplate::create($validated);

        return response()->json($showTemplate);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShowTemplate  $showTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(ShowTemplate $showTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShowTemplate  $showTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(ShowTemplate $showTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShowTemplate  $showTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShowTemplate $showTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShowTemplate  $showTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShowTemplate $showTemplate)
    {
        //
    }
}
