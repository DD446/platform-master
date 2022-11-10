<?php

namespace App\Http\Controllers\API;

use App\Classes\FeedReader;
use App\Events\FeedRemovalEvent;
use App\Events\FeedUpdateEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedRequest;
use App\Http\Requests\StoreFeedRequest;
use App\Http\Resources\FeedCollection;
use App\Http\Resources\FeedJsonResource;
use App\Http\Resources\FeedResource;
use App\Models\Feed;
use App\Models\User;
use App\Rules\UniqueFeedId;
use App\Rules\UniqueSubdomain;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FeedController extends Controller
{
    /**
     * List podcasts
     *
     * Returns a list of podcasts. Accessible with scopes: feeds,read-only-feeds *Attention* The `Example request` is not correct for this parameter. It should look like this: page[number]=5&page[size]=30 Example: page[number]=5&page[size]=30
     *
     * @group Podcasts
     * @apiResourceCollection App\Http\Resources\FeedCollection
     * @apiResourceModel App\Models\Feed
     * @queryParam page string[] Used for pagination. You can pass "number" and "size". Default: page[number] =1, page[size] = 30,
     *
     * @return FeedCollection
     * @throws \Exception
     */
    public function index()
    {
        if (Gate::forUser(auth()->user())->denies('viewFeeds')) {
            abort(403);
        }

        try {
            $maxResults = 50;
            $feeds = Feed::owner()->jsonPaginate($maxResults);
            return new FeedCollection($feeds);
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            throw new \Exception(trans('main.exception_mongo_not_available'));
        }
    }

    /**
     * Get podcast
     *
     * Returns information about a podcast (feed). Accessible with scopes: feeds,read-only-feeds
     *
     * @group Podcasts
     * @apiResource App\Http\Resources\FeedResource
     * @apiResourceModel App\Models\Feed
     * @urlParam feed_id string required ID of the podcast (feed). Example: beispiel
     *
     * @return FeedResource
     */
    public function show($feedId)
    {
/*        $request = request();
        $validated = $this->validate($request, [
            'feed_id' => ['required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ], [], [
            'feed_id' => 'ID des Podcasts', // I18N
        ]);
        $feedId = Arr::pull($validated, 'feed_id');*/
        $feed = Feed::owner()->findOrFail($feedId);
        FeedResource::withoutWrapping();

        return new FeedResource($feed);
    }

    /**
     * Create podcast
     *
     * Creates a new podcast. Accessible with scope: feeds
     *
     * @group Podcasts
     *
     * @return FeedJsonResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreFeedRequest $request)
    {
        $validated = $request->validated();
        $feedId = Arr::pull($validated, 'feed_id');

        if (app()->runningInConsole()) {
            $username = request('username');
        } else {
            $username = auth()->user()->username;
        }
        $user = User::whereUsername($username)->first();
        $feed = Feed::whereUsername($username)
            ->where(function($query) use ($feedId) {
                $query->whereFeedId($feedId)
                    ->orWhere('feed_id', '=', Str::lower($feedId));
            })
            ->first();

        if ($feed) {
            return response()->json($feed, 201);
        }

        $_feed = [
            'feed_id' => $feedId,
            'rss' => [
                'title' => 'Podcast',
                'description' => '',
                'author' => ['name' => trim($user->first_name . ' ' . $user->last_name)],
                'copyright' => null,
                'link' => config('app.url'),
                'email' => $user->email,
                'language' => 'de', # TODO: I18N
            ],
            'itunes' => [
                'title' => null,
                'subtitle' => null,
                'summary' => null,
                'explicit' => 'no',
                'block' => 'no',
                'complete' => false,
                'author' => null,
                'category' => [],
                'type' => 'episodic',
            ],
            'googleplay' => [
                'author' => null,
                'category' => 'Society &amp; Culture',
                'description' => null,
                'explicit' => 'no',
                'block' => 'no',
            ],
            'domain' => get_default_domain($username),
            'settings' => [
                'feed_entries' => Feed::FEED_COUNT_DEFAULT,
            ],
            'logo' => [
                'itunes' => null,
                'rss' => null,
                'googleplay' => null,
            ]
        ];

        // TODO: Bei Import hier zuerst die Daten aus dem Feed einfügen
        $feedUrl = Arr::pull($validated, 'feed_url');

        // Is import
        if ($feedUrl) {
            $_feed['settings']['is_importing'] = $feedUrl;

            try {
                $feed = FeedReader::getCachedFeed($feedUrl);

                if (app()->runningInConsole()) {
                    $_feed['rss']['title'] = $feed->getTitle();
                    $_feed['rss']['description'] = $feed->getDescription();
                    $_feed['rss']['author'] = $feed->getAuthor(0);
                    $_feed['rss']['copyright'] = $feed->getCopyright();
                    $_feed['rss']['link'] = $feed->getLink();
                    // TODO: E-Mail ?
                    $_feed['rss']['language'] = $feed->getLanguage();
                }

                $_feed['itunes']['title'] = $feed->getXpath()->evaluate('string('.$feed->getXpathPrefix().'/itunes:title)');
                $_feed['itunes']['subtitle'] = $feed->getSubtitle();
                $_feed['itunes']['summary'] = $feed->getSummary();
                $_feed['itunes']['author'] = $feed->getCastAuthor();
                $_feed['itunes']['explicit'] = $this->cleanExplicit($feed->getExplicit());
                $_feed['itunes']['block'] = $this->cleanBlock($feed->getBlock());
                $_feed['itunes']['complete'] = $this->cleanComplete($feed);
                $_feed['itunes']['type'] = Str::lower($feed->getPodcastType());

                $_feed['googleplay']['author'] = $feed->getPlayPodcastAuthor();
                $_feed['googleplay']['category'] = $feed->getPlayPodcastCategories() ? htmlentities(array_key_first($feed->getPlayPodcastCategories())) : 'Society &amp; Culture';
                $_feed['googleplay']['block'] = $this->cleanBlock($feed->getPlayPodcastBlock());
                $_feed['googleplay']['explicit'] = $this->cleanExplicit($feed->getPlayPodcastExplicit());
                $_feed['googleplay']['description'] = $feed->getPlayPodcastDescription();
                // TODO: $feed->getPlayPodcastImage()

                $_feed['podcastindex']['locked'] = $feed->isLocked();
                $_feed['podcastindex']['lock_owner'] = $feed->getLockOwner();
                $_feed['podcastindex']['funding'] = $feed->getFunding();
            } catch (\Exception $e) {
                Log::debug("User {$username}: Feed import error: " . $e->getMessage());
            }
        }

        if (!app()->runningInConsole()) {
            $rss = Arr::pull($validated, 'rss', []);
            $_feed['rss'] = array_intersect_key($rss, $_feed['rss']);
        }

        if (!isset($_feed['rss']['link']) || !$_feed['rss']['link']) {
            $_feed['rss']['link'] = config('app.url');
        }

        $itunes = Arr::pull($validated, 'itunes', []);
        $_feed['itunes'] = array_merge($_feed['itunes'], $itunes);

        $googleplay = Arr::pull($validated, 'googleplay', []);
        $_feed['googleplay'] = array_merge($_feed['googleplay'], $googleplay);

        $domain = Arr::pull($validated, 'domain', []);
        $isCustomSubdomain = false;

        if (isset($domain['domain']) && !empty($domain['domain']) && Str::contains($domain['domain'], '.')) {
            $domain['domain'] = Str::lower($domain['domain']);
            $domain['tld'] = Str::afterLast($domain['domain'], '.');
            // Domain is whole domain including first level domain and user given subdomain without TLD
            $domain['subdomain'] .= '.' . Str::lower(Str::beforeLast($domain['domain'], '.' . $domain['tld']));
            //$subdomain = substr($domain['domain'], 0, strlen($domain['domain']) - strlen($domain['tld'])-1);
            $domain['hostname'] = 'https://' . $domain['subdomain'] . '.' . $domain['tld'];

            $isCustomSubdomain = (Str::before($domain['subdomain'], '.') != $username);
        }
        $_feed['domain'] = array_merge($_feed['domain'], $domain);

        $logo = Arr::pull($validated, 'logo', []);
        $_feed['logo'] = array_intersect_key($logo, $_feed['logo']);

        $feed = new Feed($_feed);
        $feed->feed_id = $feedId;
        $feed->username = $username;
        $feed->entries = [];

        if (!$feed->save()) {
            throw new \Exception(trans('feeds.error_saving_feed_failed'));
        }

        if ($isCustomSubdomain) {
            Log::debug("User `{$username}`: Writing custom webserver config for feed `{$feed->feed_id}`.");
            $this->dispatch(new \App\Jobs\WriteWebserverConfig($user, $feed->feed_id));
        }

        // Is import
        if ($feedUrl) {
            \App\Jobs\GetShowsForImportedFeed::dispatch($username, $feedId)->onConnection('redis')->onQueue('importer');;
            // TODO: Import googleplay image if available
        }

        FeedResource::withoutWrapping();

        return new FeedJsonResource($feed);
    }

    /**
     * Update podcast
     *
     * Updates details of a podcast. Accessible with scope: feeds
     *
     * @group Podcasts
     * @urlParam feed_id string required ID of the podcast (feed). Example: beispiel
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(FeedRequest $request)
    {
        $msg = ['success' => trans('feeds.success_message_feed_updated')];

        $validated = $request->validated();
        $feedId = Arr::pull($validated, 'feed_id');
        $feed = Feed::owner()->findOrFail($feedId);
        $rss = $feed->rss ?? [];
        $_rss = array_merge($rss, $validated['rss'] ?? []);
        $itunes = $feed->itunes ?? [];
        $_itunes = array_merge($itunes, $validated['itunes'] ?? []);
        $googleplay = $feed->googleplay ?? [];
        $_googleplay = array_merge($googleplay, $validated['googleplay'] ?? []);

        if (!$feed->whereUsername($feed->username)
                ->whereFeedId($feedId)
                ->update(
                    [
                        'rss' => $_rss,
                        'itunes' => $_itunes,
                        'googleplay' => $_googleplay,
                    ]
                )) {
            $msg = ['error' => trans('feeds.error_message_feed_update_failed')];
        } else {
            event(new FeedUpdateEvent($feed->username, $feedId));
        }

        return response()->json($msg);
    }

    /**
     * Delete podcast
     *
     * *Caution* Removes a podcast. Accessible with scope: feeds
     *
     * @group Podcasts
     * @urlParam feed_id string required ID of the podcast (feed). Example: beispiel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($feed_id)
    {
/*        $request = request();
        $validated = $this->validate($request, [
            'feed_id' => ['required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ], [], [
            'feed_id' => 'ID des Podcasts', // I18N
        ]);
        $feedId = Arr::pull($validated, 'feed_id');*/
        $feed = Feed::owner()->findOrFail($feed_id);
        $title = $feed->rss['title'];
        $msg = trans('feeds.message_error_delete_channel', ['feed' => $title]);

        if ($feed->whereUsername(auth()->user()->username)->whereFeedId($feed->feed_id)->delete()) {
            $msg = trans('feeds.message_success_delete_channel', ['feed' => $title]);
            event(new FeedRemovalEvent($feed));
        }

        return response()->json($msg);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function sources()
    {
        $feeds = Feed::owner()->select(['feed_id', 'rss.title'])->get();

        return response()->json($feeds);
    }

    /**
     * Copy podcast
     *
     * Creates a copy of a podcast.
     * The copy is always saved with status `draft`.
     * Accessible with scope: shows
     *
     * @group Podcasts
     * @urlParam feed_id string required ID des Podcasts Example: beispiel
     * @queryParam feed_id_new string ID of the podcast the show is copied to. If omitted the same podcast as the source is used. Example: test
     * @queryParam without_shows boolean Indicates if just the podcast data without the shows of the original podcasts should be used for the copy. Example: 1
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @hideFromAPIDocumentation
     */
    public function copy($feed_id, $new_feed_id)
    {
        $this->validate(request(), [
            'new_feed_id' => [new UniqueFeedId(auth()->user()->username)], // TODO: Check for uniqueness
        ]);
        $feed = Feed::owner()->findOrFail($feed_id);

        //$feed->clone();
    }

    /**
     * @param $feed_id
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function getDefaults($feed_id)
    {
        $feed = Feed::owner()->whereFeedId($feed_id)->firstOrFail(['settings.default_item_title', 'settings.default_item_description', 'rss.author']);

        return response()->json([
            'default_title' => $feed->settings['default_item_title'] ?? '',
            'default_description' => $feed->settings['default_item_description'] ?? '',
            'default_author' => $feed->rss['author'] ?? '',
        ]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function isUrlAllowed(Request $request)
    {
        $this->validate($request, [
            'feed_id' => ['required', 'string', new UniqueFeedId(auth()->user()->username), 'regex:/^([A-Za-z0-9](?:[A-Za-z0-9\-]{0,50}[A-Za-z0-9])?$)/u'],
            'domain.subdomain' => ['required', 'string', new UniqueSubdomain(auth()->user()->username), 'regex:/^([A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?$)/u'],
            'domain.domain' => ['required', 'string', 'in:' . implode(',', config('app.local_domains'))],
        ], [], [
            'domain.domain' => trans('feeds.validation_attribute_domain'),
            'domain.subdomain' => trans('feeds.validation_attribute_subdomain'),
        ]);

        $msg = ['error' => trans('feeds.error_message_feed_update_failed')];

        return response()->json($msg);
    }

    public function cleanBlock($status)
    {
        if ($status === true) {
            $status = 'yes';
        }

        $status = strtolower($status);

        switch($status) {
            case 'yes':
                $status = 'yes';
                break;
            default:
                $status = 'no';
        }

        return $status;
    }

    public function cleanExplicit($status)
    {
        if ($status === true) {
            $status = 'yes';
        }

        $status = strtolower($status);

        switch($status) {
            case 'yes':
            case 'true':
                $status = 'yes';
                break;
            case 'clean':
            default:
                $status = 'no';
        }

        return $status;
    }

    private function cleanComplete(\Laminas\Feed\Reader\Feed\AbstractFeed $feed)
    {
        $complete = $feed->getXpath()->evaluate('string(' . $feed->getXpathPrefix() . '/itunes:complete)');

        $status = strtolower($complete);

        switch($status) {
            case 'yes':
                $status = 'yes';
                break;
            default:
                $status = 'no';
        }

        return $status;
    }

    protected function cleanEpisodeType(?string $type): string
    {
        $type = strtolower($type);

        switch($type) {
            case 'full':
            case 'trailer':
            case 'bonus':
                break;
            default:
                $type = 'full';
        }

        return $type;
    }
}
