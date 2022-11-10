<?php

namespace App\Http\Controllers;

use App\Classes\FeedSubmitter\Podcast;
use App\Classes\MediaManager;
use App\Classes\ShowManager;
use App\Http\Requests\PodcastRouletteRequest;
use App\Models\Feed;
use App\Models\PodcastRoulette;
use App\Models\PodcastRouletteMatch;
use App\Models\Show;
use App\Models\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PodcastRouletteController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $this->seo()
            ->setTitle(trans('roulette.page_title_index'));

        $pr = PodcastRoulette::owner()->where('version', '=', PodcastRoulette::ROULETTE_ROUND)->first();
        $isMatched = false;
        $match = false;
        $partner = false;

        if ($pr) {
            $feed = Feed::owner()->whereFeedId($pr->feed_id)->select(['rss.title'])->first();
            $pr->feed = $feed->rss['title'];
            $match = PodcastRouletteMatch::whereVersion(PodcastRoulette::ROULETTE_ROUND)
                ->where(function($query) use ($pr) {
                    $query
                    ->where('roulette_id', '=', $pr->id)
                    ->orWhere('roulette_partner_id', '=', $pr->id); })
                ->first();

            if ($match) {
                $isMatched = true;
                if ($match->roulette_id === $pr->id) {
                    $partner = $match->partner;
                } else {
                    $partner = $match->player;
                }
                $partnerFeed = Feed::whereUsername($partner->user->username)->whereFeedId($partner->feed_id)->select(['rss.title'])->first();
                $partner->feed_title = $partnerFeed->rss['title'];
            }
        }

        return view('roulette.index', compact('pr', 'match', 'isMatched', 'partner'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $this->seo()
            ->setTitle(trans('roulette.page_title_create'));

        $feeds = Feed::owner()->select(['feed_id', 'rss.title'])->get();

        return view('roulette.create', compact('feeds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(PodcastRouletteRequest $request)
    {
        $a = $request->validated();

        $pr = new PodcastRoulette($a);
        $pr->version = PodcastRoulette::ROULETTE_ROUND;
        $pr->user_id = auth()->user()->id;
        $pr->save();

        return redirect()->route('roulette.index')->with(['success' => trans('roulette.message_success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PodcastRoulette  $podcastRoulette
     * @return \Illuminate\Http\Response
     */
    public function show(PodcastRoulette $podcastRoulette)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PodcastRoulette  $podcastRoulette
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PodcastRoulette::owner()->findOrFail($id)->delete();

        return redirect()->route('roulette.index')->with(['success' => trans('roulette.message_destroyed')]);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'description' => ['required'],
            'recording' => ['required', 'file', 'mimes:mp3'],
            'cover' => ['file', 'mimes:jpg,png', 'max:1512', 'dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1.0'],
        ], [], [
            'description' => trans('roulette.validation_description'),
            'recording' => trans('roulette.validation_recording'),
            'cover' => trans('roulette.validation_cover'),
        ]);

        $userId = 14282;
        $feedId = 'PodcastRoulette';
        $round = PodcastRoulette::ROULETTE_ROUND;
        $user = User::find($userId);
        $mm = new MediaManager($user);
        $ret = $mm->saveFile($validated['recording']);

        if ($ret['statusCode'] != 200) {
            return redirect()->back()->with(['msg' => trans('roulette.message_file_upload_failed')]);
        }

        $pr = PodcastRoulette::owner()->whereVersion($round)->firstOrFail();
        $feed = Feed::owner()->whereFeedId($pr->feed_id)->select(['rss.title'])->first();
        $prm = PodcastRouletteMatch::with(['partner', 'player'])->whereVersion(PodcastRoulette::ROULETTE_ROUND)
            ->where(function($query) use ($pr) {
                $query->where('roulette_id', '=', $pr->id)
                    ->orWhere('roulette_partner_id', '=', $pr->id);
            })
            ->first();

        if ($prm->roulette_id === $pr->id) {
            $partner = $prm->partner;

            if (!$partner) {
                $partner = PodcastRoulette::withTrashed()->find($prm->roulette_id);
            }
        } else {
            $partner = $prm->player;
        }

        $partnerFeed = Feed::whereUsername($partner->user->username)->whereFeedId($partner->feed_id)->select(['rss.title'])->first();

        $file = $ret['file'];
        $mm->rename($file['id'], 'podcast-roulette-runde-' . $round . '-' . Str::slug($feed->rss['title']) . '-vs-' . Str::slug($partnerFeed->rss['title']) . '.' . $file['extension'], 'podcast-roulette-' . $round);

        if (isset($validated['cover']) && $validated['cover']) {
            $cRet = $mm->saveFile($validated['cover']);
            $cover = $cRet['file'];
            if ($cRet['statusCode'] == 200) {
                $mm->rename($cover['id'],
                    'podcast-roulette-runde-'.$round.'-'.Str::slug($feed->rss['title']).'-vs-'.Str::slug($partnerFeed->rss['title']).'.'.$cover['extension'],
                    'podcast-roulette-'.$round);
            }
        }

        $prm->update([
            'file_id' => $file['id'],
            'cover_id' => $cover['id'] ?? null,
            'shownotes' => $validated['description'],
        ]);

        $podcastRouletteFeed = Feed::whereUsername($user->username)
            ->where(function($query) use ($feedId) {
                $query->where('feed_id', '=', $feedId)
                    ->orWhere('feed_id', '=', Str::lower($feedId));
            })
            ->first();
        $entries = $podcastRouletteFeed->entries;
        $show = [];
        $show['title'] = 'S' . PodcastRoulette::ROULETTE_ROUND . ' E' . $prm->id . ' - ' . $feed->rss['title'] . ' vs. ' . $partnerFeed->rss['title'];
        $show['description'] = $validated['description'];
        $show['guid'] = get_guid('pod-');
        $show['show_media'] = $file['id'];
        $show['is_public'] = Show::PUBLISH_DRAFT;
        $show['lastUpdate'] = time();
        $show['author'] = $pr->podcasters . ' & ' . $partner->podcasters;
        $show['itunes'] = [
            'duration' => get_duration($user->username, $file['id']),
            'episodeType' => 'full',
            'author' =>  $pr->podcasters . ' & ' . $partner->podcasters,
        ];

        if (isset($cover['id']) && $cover['id']) {
            $show['itunes']['logo'] = $cover['id'];
        } else {
            $show['itunes']['logo'] = 1634820161;
        }

        array_push($entries, $show);

        $feed->whereUsername($user->username)
            ->where(function($query) use ($feedId) {
                $query->where('feed_id', '=', $feedId)
                    ->orWhere('feed_id', '=', Str::lower($feedId));
            })
            ->update([
                'entries' => array_values($entries)
            ]);

        return redirect()->back()->with(['msg' => trans('roulette.message_file_upload_success')]);
    }
}
