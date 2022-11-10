<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use Illuminate\Http\Request;
use App\Models\Feed;
use App\Models\User;

class CampaignInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('campaigns.index');

/*        $invitations = [];

        return view('campaigns.invitations.index', compact('invitations'));*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkAdvertiserGrant();

        $campaigns = Campaign::owner()->get();

        if ($campaigns->count() < 1) {
            return redirect()->route('campaigns.create')->with(['status' => 'Du musst zuerst eine Kampagne anlegen.']);
        }

        if (!\request('campaign_id')) {
            return redirect()->route('campaigns.index')->with(['error' => 'Du musst eine Kampagne auswählen']);
        }

        $campaign = Campaign::owner()->findOrFail(\request('campaign_id'));
        $categories = explode('|', $campaign->itunes_category);
        $feeds = Feed::where('settings.ads', '=', "1")->whereIn('itunes.category', $categories)->get();
        $users = [];
        $invitations = CampaignInvitation::where('campaign_id', '=', $campaign->id)->get();

        foreach ($feeds as $feed) {
            $user = User::where('username', '=', $feed->username)->first();

            if ($user) {
                if (!$invitations->contains('user_id', $user->usr_id)) {
                    $users[$feed->username][] = [
                        'id' => $feed->feed_id,
                        'image_id' => $feed->logo['itunes'] ?? null,
                        'title' => $feed->rss['title'],
                        'description' => $feed->rss['description'],
                        'author' => $feed->rss['author'],
                        'disabled' => $invitations->contains('user_id', $user->usr_id),
                    ];
                }
            }
        }

        return view('campaigns.invitations.create', compact('campaign', 'users'));
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
            'campaign_id' => 'required|exists:campaigns,id',
            'users' => 'required',
        ]);

        $campaign = Campaign::owner()->where('id', '=', $request->campaign_id)->firstOrFail();
        $counter = 0;

        foreach ($request->users as $aUser) {
            foreach ($aUser as $username => $feed_id) {
                $user = User::where('username', '=', $username)->first();
                $feed = Feed::where('username', '=', $username)->where('feed_id', '=', $feed_id)->first();

                if ($user) {
                    $invitation = new CampaignInvitation();
                    $invitation->campaign_id = $campaign->id;
                    $invitation->user_id = $user->usr_id;
                    $invitation->feed_id = $feed_id;

                    try {
                        if ($invitation->save()) {
                            $user->sendCampaignInvitation($campaign, $feed);
                            $counter++;
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                    }
                }
            }
        }

        return redirect()->route('campaigns.index')->with(['success' => 'Du hast die Anfrage erfolgreich an ' . $counter . ' Podcaster verschickt.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignInvitation $campaignInvitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return \Illuminate\Http\Response
     */
    public function edit(CampaignInvitation $campaignInvitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignInvitation $campaignInvitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignInvitation $campaignInvitation)
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
