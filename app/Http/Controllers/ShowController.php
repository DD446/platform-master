<?php

namespace App\Http\Controllers;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Classes\Domain;
use App\Classes\FeedGeneratorManager;
use App\Classes\ShowManager;
use App\Jobs\SyncShowToBlog;
use Artesaos\SEOTools\Traits\SEOTools;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JamesHeinrich\GetID3\GetID3;
use Khill\Duration\Duration;
use App\Classes\FileTagManager;
use App\Events\FeedUpdateEvent;
use App\Events\ShowAddEvent;
use App\Http\Requests\ShowRequest;
use App\Http\Resources\EntryCollection;
use App\Models\Feed;
use App\Models\Show;

class ShowController extends Controller
{
    use SEOTools;

    /**
     * List episodes
     *
     * Returns a list of episodes (shows) belonging to a podcast feed.
     *
     * @group Episodes
     * @apiResourceCollection App\Http\Resources\EntryCollection
     * @bodyParam feed string The id of a podcast (feed). Example: beispiel
     * @responseFile responses/shows.get.json
     *
     * @param  Request  $request
     * @param  null  $feedId feed id
     * @return EntryCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, string $feedId)
    {
        $feed = Feed::owner()->findOrFail($feedId);

        if (request()->wantsJson()) {

            $_items =  $feed->shows;
            $filter = $request->filter;

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
                    }

                    if (in_array($match, [0, 1, 2], true)) {
                        $_items = $_items->filter(function ($item, $key) use ($match) {
                            return Str::contains($item->is_public, $match);
                        });
                    } else if ($match === 'noenclosure') {
                        $_items = $_items->filter(function ($item, $key) use ($match) {
                            return !$item->show_media;
                        });
                    } else if ($match === 'nocover') {
                        $_items = $_items->filter(function ($item, $key) use ($match) {
                            return !isset($item->itunes['logo']) || !is_numeric($item->itunes['logo']);
                        });
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
                        $_items = $_items->filter(function ($item, $key) use ($filter) {
                            return Str::contains($item->title, $filter);
                        });
                    }
                }
            }

            $sortDesc = filter_var(request('sortDesc', false), FILTER_VALIDATE_BOOLEAN);
            $sortBy = request('sortBy', 'title');

            switch ($sortBy) {
                case 'lastUpdate' :
                    $_items = $_items->sortBy(function($o, $b) {
                        return $o->lastUpdate;
                    }, \SORT_NUMERIC, $sortDesc);
                    break;
                case 'is_public' :
                    $_items = $_items->sortBy(function($o, $b) {
                        return trans_choice('shows.text_choice_state', $o->is_public);
                    }, \SORT_STRING, $sortDesc);
                    break;
                default:
                    $_items = $_items->sortBy($sortBy, \SORT_REGULAR, $sortDesc);
            }

            $currentPage = request('currentPage', 1);
            $perPage = request('perPage', 10);
            // Count has to come before slicing and after filtering
            $a['count'] = count($_items);

            while(($currentPage-1)*$perPage > $a['count']) {
                $currentPage -= 1;
            }
            // Only slice if result is not empty
            if ($perPage < $a['count']) {
                $_items = $_items->slice(($currentPage - 1) * $perPage, $perPage);
            }

            $_items->map(function($show) use ($feed) {
                $show['domain'] = $feed->domain;
                $show['username'] = $feed->username;
                return $show;
            });
            $request->feed = $feedId;

            $_shows = [
                'items' => new EntryCollection($_items),
                'labels' => [],
                'count' => $feed->shows->count(),
            ];

            return response()->json($_shows);
        }

/*        $this->seo()->setTitle(trans('shows.page_title_index', ['feed' => $feed->rss['title']]));

        return view('shows.index', compact(['feedId', 'feed']));*/
        return redirect()->to(route('feeds') . '#/podcast/' . $feedId . '/episoden');
    }

    /**
     * Add episode
     *
     * Show the form for creating a new resource.
     *
     * @param $feedId
     * @return Factory|RedirectResponse|Response|View
     */
    public function create($feedId)
    {
        $feed = Feed::owner()->whereFeedId($feedId)->first();

        $this->seo()->setTitle(trans('shows.page_title_create', ['feed' => $feed->rss['title']]));

        if (!$feed) {
            abort(404);
        }

/*        $countShows = $feed->shows->count();
        $canSchedulePosts = has_package_feature(auth()->user()->package, Package::FEATURE_SCHEDULER);
        $guid = get_guid('pod-');

        return view('shows.create', compact('feed', 'guid', 'countShows', 'canSchedulePosts'));*/
        return redirect()->to(route('feeds') . '#/podcast/' . $feedId . '/episode');
    }

    /**
     * Add episode
     *
     * @group Episodes
     * @bodyParam feed_id string Podcast to which this show is to be added. Example: beispiel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(ShowRequest $request)
    {
        $username = auth()->user()->username;
        $msg = ['success' => trans('shows.success_message_add_show')];

        $validated = $request->validated();
        $feedId = Arr::pull($validated, 'feed_id');
        $feed = Feed::owner()->whereFeedId($feedId)->firstOrFail();
        $entries = $feed->entries ?? [];

        $publishingDate = Arr::pull($validated, 'publishing_date');
        $publishingTime = Arr::pull($validated, 'publishing_time');
        $published = new Carbon($publishingDate, config('app.timezone'));
        $published->setTimeFromTimeString($publishingTime);
        $validated['lastUpdate'] = $published->getTimestamp();
        $validated['description'] = trim($validated['description']);

        if (isset($feed->settings['audiotakes'])
            && $feed->settings['audiotakes'] == 1
            && isset($validated['show_media'])) {
            $file = get_file($feed->username, $validated['show_media']);
            // Should always be true at this point
            if ($file) {
                $validated['audiotakes_guid'] = sha1($file['name']);
            }
        }

        if ($published > now() && $validated['is_public'] != Show::PUBLISH_DRAFT) {
            $validated['is_public'] = Show::PUBLISH_FUTURE;
        }

        array_push($entries, $validated);

        $res = $feed->whereUsername($username)
            ->whereFeedId($feedId)
            ->update([
                'entries' => array_values($entries)
            ]);

        if (!$res) {
            $msg = ['error' => trans('shows.error_message_add_show')];
        } else {
            event(new FeedUpdateEvent($username, $feedId));
            event(new ShowAddEvent($username, $feedId, $validated['guid']));

            if (in_array($validated['is_public'], [Show::PUBLISH_PAST, Show::PUBLISH_NOW])) {
                SyncShowToBlog::dispatch($feed, $validated['guid']);
            }
            LaravelMatomoTracker::queueEvent('show', 'saved', $username, \Carbon\CarbonImmutable::now()->toFormattedDateString());
        }

        return response()->json($msg);
    }

    public function edit($id, $guid)
    {
        return redirect()->to('/podcast/' . $id . '/episoden/#edit/' . $guid);
    }

    /**
     * @param  string  $feedId
     * @param  string  $guid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function share(string $feedId, string $guid)
    {
        $showManager = new ShowManager();
        $feed = Feed::owner()->whereFeedId($feedId)->first();

        if (!$feed) {
            abort(404);
        }

        $show = $showManager->get($feed, $guid);

        if (!$show) {
            abort(404);
        }

        $this->seo()
            ->setTitle(trans('shows.page_title_share', ['feed' => $feed->rss['title'], 'title' => $show['title']]));

        $sharingOpts = [
            ['value' => null, 'text' => trans('shows.label_choose_sharing_site'), 'disabled' => true],
        ];

        if ($show['link']) {
            array_push($sharingOpts, ['value' => $show['link'], 'text' => trans('shows.label_sharing_opt_show')]);
        }

        // TODO: Blog
        $fgm = new FeedGeneratorManager($feed->username, $feed->feed_id);
        if (isset($feed->domain['website_type']) && $feed->domain['website_type'] === Feed::WEBSITE_TYPE_WORDPRESS
            && (isset($feed->domain['website_redirect']) && !$feed->domain['website_redirect']
                || !isset($feed->domain['website_redirect']))) {
            $blogLink = get_link(strtolower($feedId), $feed['domain']['hostname'], null). '/' . $fgm->getBlogTitle($show['title']) . '/';

            if ($blogLink && $blogLink != $show['link']) {
                array_push($sharingOpts, ['value' => $blogLink, 'text' => trans('shows.label_sharing_opt_blog')]);
            }
        }

        if (isset($feed->submit_links) && isset($feed->submit_links['podcast']) && $feed->submit_links['podcast']) {
            if (Str::startsWith($feed->submit_links['podcast'], 'https://www.podcast.de/podcast/')) {
                array_push($sharingOpts,
                    ['value' => $feed->submit_links['podcast'], 'text' => trans('shows.label_sharing_opt_podcastde')]);
            }
        }

/*        if (isset($feed->submit_links) && isset($feed->submit_links['spotify']) && $feed->submit_links['spotify']) {
            if (Str::startsWith($feed->submit_links['spotify'], 'https://open.spotify.com/show/')) {
                array_push($sharingOpts,
                    ['value' => $feed->submit_links['spotify'], 'text' => trans('shows.label_sharing_opt_spotify')]);
            }
        }*/

/*        array_push($sharingOpts,
            ['value' => route('api.shows.show.share', ['feed_id' => $feed->feed_id, 'guid' => $guid, 'username' => $feed->username]), 'text' => trans('shows.label_sharing_opt_api')]);*/

        array_push($sharingOpts, ['value' => 'https://', 'text' => trans('shows.label_sharing_opt_custom')]);

        return view('shows.share', compact('feed', 'show', 'sharingOpts'));
    }

    public function copy($feed, $id)
    {
        $showManager = new ShowManager();
        $feed = Feed::owner()->where('feed_id', '=', $feed)->firstOrFail();
        $msg = ['success' => trans('shows.success_message_copy_show')];

        if (!$showManager->copy($feed, $id)) {
            $msg = ['error' => trans('shows.error_message_copy_show')];
        }

        return response()->json($msg);
    }

    public function showDeleteDuplicateForm()
    {
        if (Gate::forUser(auth()->user())->denies('deleteDuplicateShow')) {
            abort(403);
        }

        return view('admin.channel.deleteduplicateform');
    }

    public function deleteDuplicate()
    {
        if (Gate::forUser(auth()->user())->denies('deleteDuplicateShow')) {
            abort(403);
        }

        $showManager = new ShowManager();
        $guid = \request('guid');
        $feed = Feed::where('username', '=', \request('user'))->where('feed_id', '=', \request('feed'))->firstOrFail();
        $msg = ['success' => "Die Episode wurde gelöscht."];

        if (!$showManager->delete($feed, $guid)) {
            $msg = ['error' => "Die Episode wurde nicht gelöscht."];
        }

        return redirect()->back()->with($msg);
    }

    public function getGuid()
    {
        return response()->json(get_guid('pod-'));
    }

    public function getDuration(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $id = request('id');
        $duration = get_duration(auth()->user()->username, $id);

        return response()->json($duration);
    }

    public function getImageSuggestions(Request $request)
    {
        $this->validate($request, [
            'feedId' => 'required'
        ]);

        $user = auth()->user();
        $images = $user->getFiles('created', true, 'type:logo', 1, 3);
        $feedId = request('feedId');
        $exclude = collect($images['items'])->map(function($item) { return $item->id; })->toArray();
        $logos = $this->getLogoSuggestions($user->username, $feedId, $exclude);

        foreach($images['items'] as &$image) {
            unset($image->path);
        }

        $results = [
            'images' => $images,
            'logos' => $logos,
        ];
        return response()->json($results);
    }

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @param  array  $exclude
     * @return array[]
     * @throws \Exception
     */
    private function getLogoSuggestions(string $username, string $feedId, array $exclude): array
    {
        $feed = Feed::whereUsername($username)
            ->whereFeedId($feedId)
            ->select(['entries', 'logo.itunes'])
            ->firstOrFail();
        $items = [];
        $fileId = $feed->logo['itunes'];
        $limit = 3;

        if ($fileId && !in_array($fileId, $exclude)) {
            $file = get_file($username, $fileId);
            array_push($items, (object) $file);
            --$limit;
        }

        $entries = collect($feed->entries);
        $_entries = $entries
            ->map(function($item) { if (isset($item['itunes']['logo']) && !empty($item['itunes']['logo'])) { return $item['itunes']['logo']; } })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->toArray();

        foreach ($_entries as $fileId => $cnt) {
            if (!$limit) break;
            if ($fileId && !in_array($fileId, $exclude)) {
                $file = get_file($username, $fileId);
                if ($file && is_array($file)) {
                    unset($file['path']);
                    array_push($items, (object) $file);
                    --$limit;
                }
            }
        }

        $files = [];

        if (count($items) > 0) {
            $files['items'] = $items;
        }

        return $files;
    }

    public function getFileSuggestions()
    {
        $currentPage = \request('page', 1);
        $perPage = \request('limit', 3);
        $files = auth()
            ->user()
            ->getFiles('created', true, 'type:enclosure', $currentPage, $perPage);

        return response()->json($files);
    }

    public function getMetaData(Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required|numeric',
            'type' => ['nullable', 'in:' . FileTagManager::DATA_TYPE_COMMON . ',' . FileTagManager::DATA_TYPE_CHAPTERS]
        ]);

        $id = request('id');
        $file = get_file(auth()->user()->username, $id);
        $data = [];

        if ($file) {
            $ftm = new FileTagManager($file);
            $data = $ftm->getData($validated['type']);
        }

        return response()->json($data);
    }

    public function wizard()
    {
        $this->seo()
            ->setTitle(trans('shows.page_title_wizard'));

        $feeds = Feed::owner()->select(['feed_id', 'rss.title'])->get();

        if ($feeds->count() < 1) {
            return redirect('/beta/podcast/erstellen');
        } else if ($feeds->count() === 1) {
            return redirect()->route('show.create', ['id' => $feeds[0]->feed_id]);
        }

        return view('shows.wizard', compact('feeds'));
    }
}
