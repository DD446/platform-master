<?php

namespace App\Http\Controllers;

use App\Classes\Datacenter;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkAdvertiserGrant();

        $campaigns = Campaign::owner()->get();

        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkAdvertiserGrant();

        $campaign = new Campaign();
        $itunesCategories = Datacenter::getItunesCategories();

        return view('campaigns.create', compact('campaign', 'itunesCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkAdvertiserGrant();

        $this->validate($request, [
            'title' => 'required|max:255|unique:campaigns',
            'description' => 'required',
            'name' => 'required',
            'reply_to' => 'required|email',
            'itunes_category' => 'required|array|min:1|max:5',
        ], [], [
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'name' => 'Name des Ansprechpartners',
            'reply_to' => 'Antwort-Adresse',
            'itunes_category' => 'Kategorien',
        ]);

        $campaign = new Campaign();
        $campaign->fill($request->except('itunes_category'));
        $campaign->advertiser_id = auth()->user()->getAuthIdentifier();
        $campaign->itunes_category = implode('|', $request->itunes_category);

        if (!$campaign->save()) {
            return redirect()->back()->with(['error' => 'Die Kampagne konnte nicht angelegt werden.']);
        }

        return redirect()->route('invitations.create', ['campaign_id' => $campaign->id])->with(['success' => 'Die Kampagne wurde erfolgreich angelegt. Du kannst jetzt Podcaster einladen.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        //
    }

    private function checkAdvertiserGrant()
    {
        if (!auth()->user()->is_advertiser) {
            abort(404, 'Bitte stelle einen Antrag als Werbetreibender.');
        }
    }
}
