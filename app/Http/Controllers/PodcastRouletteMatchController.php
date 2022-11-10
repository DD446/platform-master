<?php

namespace App\Http\Controllers;

use App\Classes\FeedSubmitter\Podcast;
use App\Models\PodcastRoulette;
use App\Models\PodcastRouletteMatch;
use App\Models\User;
use Illuminate\Http\Request;

class PodcastRouletteMatchController extends Controller
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
        if (!in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            abort(409);
        }

        $validated =$this->validate($request, [
            'matching' => ['required', 'in:random,selected'],
            'roulette_id' => ['required_if:matching,selected', 'different:roulette_partner_id'],
            'roulette_partner_id' => ['required_if:matching,selected'],
        ],[],[
            'roulette_id' => 'Podcaster',
            'roulette_partner_id' => 'Podcast-Partner',
        ]);

        if ($validated['matching'] == 'random') {
            $participants = PodcastRouletteMatch::whereVersion(PodcastRoulette::ROULETTE_ROUND)->get();
            $ids = $participants->pluck('roulette_id');
            $partnerIds = $participants->pluck('roulette_partner_id');
            $allIds = $ids->concat($partnerIds);
            $unmatched = PodcastRoulette::whereVersion(PodcastRoulette::ROULETTE_ROUND)->whereNotIn('id', $allIds->toArray())->get()->shuffle();
            $chunks = $unmatched->chunk(2);

            foreach($chunks as $chunk) {
                if (count($chunk) == 2) {
                    $player = $chunk->pop();
                    $partner = $chunk->pop();
                    // TODO: E-Mail-Versand ist buggy
                    PodcastRouletteMatch::create(['roulette_id' => $player->id, 'roulette_partner_id' => $partner->id, 'version' => PodcastRoulette::ROULETTE_ROUND]);
                }
            }
        } else if ($validated['matching'] == 'selected') {
            PodcastRouletteMatch::create(['roulette_id' => $validated['roulette_id'], 'roulette_partner_id' => $validated['roulette_partner_id'], 'version' => PodcastRoulette::ROULETTE_ROUND]);
        }

        return redirect()->back();
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
     * @param  \App\Models\PodcastRoulette  $podcastRoulette
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(PodcastRoulette $podcastRoulette)
    {
        if (!in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            abort(409);
        }

        $matches = PodcastRouletteMatch::with(['player', 'partner'])->whereVersion(PodcastRoulette::ROULETTE_ROUND)->get();
        $ids = $matches->pluck('roulette_id');
        $partnerIds = $matches->pluck('roulette_partner_id');
        $allIds = $ids->concat($partnerIds);

        $unmatched = PodcastRoulette::with(['user'])->whereVersion(PodcastRoulette::ROULETTE_ROUND)->whereNotIn('id', $allIds->toArray())->get();

        return view('roulette.match.edit', compact('matches', 'unmatched'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PodcastRoulette  $podcastRoulette
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PodcastRoulette $podcastRoulette)
    {
        if (!in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            abort(409);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
