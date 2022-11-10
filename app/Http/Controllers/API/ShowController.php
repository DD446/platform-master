<?php

namespace App\Http\Controllers\API;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Classes\ShowManager;
use App\Events\FeedUpdateEvent;
use App\Events\ShowAddEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowRequest;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\FeedCollection;
use App\Http\Resources\ShowResource;
use App\Jobs\SyncShowToBlog;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\Show;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ShowController extends Controller
{
    /**
     * List episodes
     *
     * Returns a list of episodes (shows) belonging to a podcast feed. Accessible with scopes: shows,read-only-shows
     *
     * @group Shows
     * @apiResourceModel App\Models\Show
     * @apiResourceCollection App\Http\Resources\EntryCollection
     * @responseFile resources/responses/shows.get.json
     * @urlParam feed_id string required ID des Podcasts Example: beispiel
     * @bodyParam filter string Suche in Titel und Beschreibung nach bestimmten Episoden.  Example: Kurzfilm
     * @bodyParam page string[] Used for pagination. You can pass "number" and "size". Default: page[number] =1, page[size] = 30  *Attention* The `Example request` is not correct for this parameter. It should look like this: page[number]=5&page[size]=30 Example: page[number]=5&page[size]=30
     * @queryParam sortBy
     * @queryParam sortDesc
     *
     * @return EntryCollection|\Illuminate\Contracts\Foundation\Application
     */
    public function index(string $feedId)
    {
        $request = request();
        $validated = $this->validate($request, [
            'page' => 'array:number,size',
            'page.number' => ['nullable', 'numeric', 'min:1'],
            'page.size' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'filter' => ['nullable'],
            'sortBy' => ['nullable'],
            'sortDesc' => ['nullable'],
        ], [], [
            'feed_id' => 'ID des Podcasts',
            'page.number' => 'Seite',
            'page.size' => 'Anzahl Ergebnisse', // I18N
        ]);
        $feed = Feed::owner()->findOrFail($feedId);
        $username = $feed->username;
        $shows = $feed->shows;
        $shows = $shows->map(function($show) use ($feed) {
            $show['domain'] = $feed->domain;
            $show['username'] = $feed->username;
            return $show;
        });

        $filter = $validated['filter'] ?? null;

        if (!is_null($filter)) {
            if ($filter != 'null') {
                if (preg_match('/status:("[^"]*+"|[^"\s]*+)/', $filter, $matches)) {
                    array_shift($matches);
                    switch($matches[0]) {
                        case 'draft':
                            $match = 0;
                            break;
                        case 'published':
                            $match = 1;
                            break;
                        case 'scheduled':
                            $match = 2;
                            break;
                        default:
                            $match = mb_strtolower($matches[0]);
                    }

                    if (in_array($match, [0, 1, 2], true)) {
                        $shows = $shows->filter(function ($item, $key) use ($match) {
                            return Str::contains($item->is_public, $match);
                        });
                    } else if ($match === 'noenclosure') {
                        $shows = $shows->filter(function ($item, $key) use ($match, $username) {
                            return !$item->show_media || !get_file($username, $item->show_media);
                        });
                    } else if ($match === 'nocover') {
                        $shows = $shows->filter(function ($item, $key) use ($match) {
                            return !isset($item->itunes['logo']) || !is_numeric($item->itunes['logo']);
                        });
                    }
                }

                // Skip predefined filters
                $filter = preg_replace('/(status):("[^"]*+"|[^"\s]*+)/', '', $filter);
                $filter = trim($filter);

                if ($filter) {
                    /*                    $items = $_items->filter(function ($a) use ($filter, $strict) {
                                            if ($strict) {
                                                return $a->name === $filter;
                                            } else {
                                                return stripos($a->name, $filter) !== false;
                                            }
                                        });*/
                    $shows = $shows->filter(function ($item, $key) use ($filter) {
                        return Str::contains($item->title, $filter);
                    });
                }
            }
        }

        $sortDesc = filter_var(request('sortDesc', false), FILTER_VALIDATE_BOOLEAN);
        $sortBy = request('sortBy', 'title');

        switch ($sortBy) {
            case 'lastUpdate' :
                $shows = $shows->sortBy(function($o, $b) {
                    return $o->lastUpdate;
                }, \SORT_NUMERIC, $sortDesc);
                break;
            case 'is_public' :
                $shows = $shows->sortBy(function($o, $b) {
                    return trans_choice('shows.text_choice_state', $o->is_public);
                }, \SORT_STRING, $sortDesc);
                break;
            default:
                $shows = $shows->sortBy($sortBy, \SORT_REGULAR, $sortDesc);
        }

        if ($request->filled('page')) {
            $pageNumber = $validated['page']['number'] ? $validated['page']['number']-1 : 0;
            $pageSize = $validated['page']['size'] ?? 10;
            $shows = $shows->slice($pageNumber*$pageSize, $pageSize);
        }

        return new EntryCollection($shows);
    }

    /**
     * Get episode
     *
     * Gets details of an episode. Accessible with scopes: shows,read-only-shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @responseFile resources/responses/show.get.json
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     */
    public function show(string $feed_id, string $guid)
    {
        $feed = Feed::owner()->whereFeedId($feed_id)->firstOrFail();

        $showManager = new ShowManager();
        $show = $showManager->get($feed, $guid);

        if (request()->wantsJson()) {
            return new ShowResource((object)$show);
        }

        $show = new ShowResource((object)$show);

        return view('shows.api.show', compact('show'));
    }

    /**
     * @param  string  $username
     * @param  string  $feed_id
     * @param  string  $guid
     * @return ShowResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     * @hideFromAPIDocumentation
     */
    public function share(string $username, string $feed_id, string $guid)
    {
        $feed = Feed::where($username)->whereFeedId($feed_id)->firstOrFail();

        $showManager = new ShowManager();
        $show = $showManager->get($feed, $guid);
        $show = new ShowResource((object)$show);

        if (request()->wantsJson()) {
            return $show;
        }
        //$a = $show->toArray(\request());
        return view('shows.api.show', compact('show'));
    }

    /**
     * Update episode
     *
     * Updates details of an episode. Accessible with scope: shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     *
     * @param  ShowRequest  $request
     */
    public function update(string $feedId, string $guid)
    {
        $msg = ['success' => trans('shows.success_message_update_show')];

        $feed = Feed::owner()->whereFeedId($feedId)->firstOrFail();

        $validated = $this->validate(request(), [
            'guid' => 'required|max:255',
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'copyright' => 'nullable|max:255',
            'is_public' => 'required|in:-1,0,1,2',
            'description' => 'required|max:4000',
            'itunes.title' => 'nullable|max:255',
            'itunes.subtitle' => 'nullable|max:255',
            'itunes.summary' => 'nullable|max:4000',
            'itunes.episode' => 'nullable|numeric',
            'itunes.episodeType' => 'required|in:full,trailer,bonus',
            'itunes.season' => 'nullable|numeric',
            'itunes.logo' => 'nullable|numeric',
            'itunes.duration' => 'nullable',
            'itunes.explicit' => 'nullable|in:yes,true,false,0,1',
            'itunes.isclosedcaptioned' => 'nullable|in:yes',
            'itunes.author' => 'nullable|max:255',
            'publishing_date' => 'required|date',
            'publishing_time' => 'required',
            'link' => 'nullable|url',
            'show_media' => 'nullable|numeric',
        ]);

        $publishingDate = Arr::pull($validated, 'publishing_date');
        $publishingTime = Arr::pull($validated, 'publishing_time');
        $published = new Carbon($publishingDate, config('app.timezone'));
        $published->setTimeFromTimeString($publishingTime);
        $validated['lastUpdate'] = $published->getTimestamp();

        if ($published > now() && $validated['is_public'] != Show::PUBLISH_DRAFT) {
            $validated['is_public'] = Show::PUBLISH_FUTURE;
        } elseif ($validated['is_public'] === Show::PUBLISH_PAST) {
            $validated['is_public'] = Show::PUBLISH_NOW;
        }
        $entries = $feed->entries;

        foreach ($entries as &$show) {
            if (isset($show['guid']) && $show['guid'] === $guid) {
                $show = array_merge($show, $validated);
            }
        }

        $res = $feed->whereUsername(auth()->user()->username)
            ->whereFeedId($feedId)
            ->update([
                'entries' => array_values($entries)
            ]);

        if (!$res) {
            $msg = ['error' => trans('shows.error_message_update_show')];
        } else {
            event(new FeedUpdateEvent(auth()->user()->username, $feedId));
            event(new ShowAddEvent(auth()->user()->username, $feedId, $guid));
        }
        SyncShowToBlog::dispatch($feed, $guid);

        return response()->json($msg);
    }

    /**
     * Create episode
     *
     * Adds a new episode. Accessible with scope: shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     *
     * @param  ShowRequest  $request
     */
    public function store(ShowRequest $request)
    {
    }

    /**
     * Delete episode
     *
     * Removes a show from a podcast (and the feed). Accessible with scope: shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(string $feedId, string $guid)
    {
        $showManager = new ShowManager();
        $feed = Feed::owner()->where('feed_id', '=', $feedId)->firstOrFail();
        $msg = ['success' => trans('shows.success_message_destroy_show')];

        if (!$showManager->delete($feed, $guid)) {
            $msg = ['error' => trans('shows.error_message_destroy_show')];
        } else {
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));
            $show = $showManager->get($feed, $guid);

            if (isset($show->sync_id) && !empty($show->sync_id)) {
                $this->dispatch(new \App\Jobs\DeleteBlogEntry($feed->username, $feed, $show->sync_id));
            }
        }

        return response()->json($msg);
    }

    /**
     * Copy episode
     *
     * Creates a copy of a show. You can create a duplicate within the same podcast or copy the show to another podcast.
     * The copy is always saved with status `draft`.
     * Accessible with scope: shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     * @queryParam feed_id_to string ID of the podcast the show is copied to. If omitted the same podcast as the source is used. Example: test
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function copy(string $feedId, string $guid)
    {
        $request = request();
        $username = auth()->user()->username;
        $validated = $this->validate($request, [
            'feed_id_to' => ['nullable', 'exists:App\Models\Feed,feed_id,username,' . $username],
        ], [], [
            'feed_id_to' => 'ID eines Podcasts', // I18N
        ]);

        $feedIdTo = Arr::pull($validated, 'feed_id_to');
        $feedTo = null;
        $showManager = new ShowManager();
        $feed = Feed::owner()->where('feed_id', '=', $feedId)->firstOrFail();

        if ($feedIdTo) {
            $feedTo = Feed::owner()->where('feed_id', '=', $feedIdTo)->firstOrFail();
        }
        $msg = ['success' => trans('shows.success_message_copy_show')];

        if (!$showManager->copy($feed, $guid, $feedTo)) {
            $msg = ['error' => trans('shows.error_message_copy_show')];
        }
        LaravelMatomoTracker::queueEvent('show', 'copied', $username, \Carbon\CarbonImmutable::now()->toFormattedDateString());

        return response()->json($msg);
    }

    /**
     * Move episode
     *
     * Moves a show from one podcast to another.
     *
     * Accessible with scope: shows
     *
     * @group Shows
     * @urlParam guid string required GUID einer Episode. Example: pod-1234567890
     * @queryParam feed_id string required ID des Podcasts Example: beispiel
     * @queryParam feed_id_to string required ID of the podcast the show is moved to. Example: test
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function move(string $feedId, string $guid)
    {
        $request = request();
        $validated = $this->validate($request, [
            'feed_id_to' => ['required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ], [], [
            'feed_id_to' => 'ID eines Podcasts', // I18N
        ]);

        $feedIdTo = Arr::pull($validated, 'feed_id_to');
        $showManager = new ShowManager();
        $feed = Feed::owner()->where('feed_id', '=', $feedId)->firstOrFail();
        $feedTo = Feed::owner()->where('feed_id', '=', $feedIdTo)->firstOrFail();
        $msg = ['success' => trans('shows.success_message_move_show')];

        if (!$showManager->move($feed, $guid, $feedTo)) {
            $msg = ['error' => trans('shows.error_message_move_show')];
        }

        return response()->json($msg);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function combined()
    {
        $request = request();
        $validated = $this->validate($request, [
            'i' => ['nullable', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
            's' => ['nullable', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ], [], [
            'i' => trans('shows.validation_attribute_i'),
            's' => trans('shows.validation_attribute_s'),
        ]);

        if (isset($validated['i']) && $validated['i']) {
            $feedId = AudiotakesContract::owner()->where('identifier', '=', $validated['i'])->value('feed_id');
            $feedIds = [ $feedId ];
        } else {
            $feedIds = AudiotakesContract::owner()->get()->pluck('feed_id');
        }

        $feeds = Feed::owner()
            ->when($feedIds, function($query) use ($feedIds) {
                return $query->whereIn('feed_id', $feedIds);
            })
            ->get();

        $_feeds = (new FeedCollection($feeds))->additional(['withShows' => true]);

        return response()->json($_feeds);
    }
}
