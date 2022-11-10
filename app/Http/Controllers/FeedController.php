<?php

namespace App\Http\Controllers;

use App\Classes\NginxServerConfigWriter;
use App\Jobs\CreateBlog;
use App\Jobs\DeleteBlog;
use App\Jobs\DeleteBlogFeed;
use App\Jobs\SyncShowToBlog;
use App\Jobs\UpdateBlogDomain;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Classes\DomainManager;
use App\Events\FeedRemovalEvent;
use App\Events\FeedUpdateEvent;
use App\Events\ShowAddEvent;
use App\Http\Resources\FeedResource;
use App\Http\Resources\FeedCollection;
use App\Models\Feed;
use App\Models\Package;
use App\Models\User;
use App\Models\UserOauth;
use SEO;

class FeedController extends Controller
{
    use SEOTools;

    /**
     * List podcasts
     *
     * Returns a list of podcasts.
     *
     * @group Podcasts
     *
     * @param  Request  $request
     * @return FeedCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if (Gate::forUser(auth()->user())->denies('viewFeeds')) {
            abort(403);
        }

        $this->seo()
            ->setTitle(trans('feeds.page_title_index'));

        $canEmbed = Gate::forUser(auth()->user())->allows('viewPlayerConfigurator');
        $hasAuphonic = Gate::forUser(auth()->user())->allows('hasAuphonicFeature')
            &&  UserOauth::owner()->service('auphonic')->count() > 0;
        $customDomainFeature = get_package_feature_custom_domains(auth()->user()->package, auth()->user());
        $canSchedulePosts = has_package_feature(auth()->user()->package, Package::FEATURE_SCHEDULER);
        $hasFeeds = Feed::owner()->count() > 0;

        $canCreatePlayerConfigurations = Gate::forUser(auth()->user())->allows('canSavePlayerConfigurations');
        $canUseCustomPlayerStyles = Gate::forUser(auth()->user())->allows('canUseCustomStylesForPlayer');
        $amountPlayerConfigurations = get_player_configuration_count(auth()->user())['total'];

        return view('feeds.index', compact('canEmbed', 'hasAuphonic', 'customDomainFeature', 'canSchedulePosts', 'hasFeeds', 'canCreatePlayerConfigurations', 'canUseCustomPlayerStyles', 'amountPlayerConfigurations'));
    }

    /**
     * Get podcast
     *
     * Returns information about a podcast (feed).
     *
     * @group Podcasts
     * @apiResource App\Http\Resources\FeedResource
     *
     * @param  Request  $request
     * @param $id
     * @return FeedResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        $feed = Feed::owner()->select(['feed_id','username','rss','itunes','googleplay','podcast'])->findOrFail($id);

        if ($request->is('api/*')) {
            FeedResource::withoutWrapping();

            return new FeedResource($feed);
        }

/*        SEO::setTitle(trans('feeds.page_title_feed', ['feed' => $feed->rss['title']]));

        return view('feeds.show', compact('feed'));*/
        return redirect()->to(route('feeds') . '#/podcast/' . $id);
    }

    /**
     * Update podcast
     *
     * Updates details of a podcast.
     *
     * @group Podcasts
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $feed = Feed::owner()->findOrFail($id);
        $content = trim(\request('content'));

        switch (\request('type')) {
            case 'title':
            case 'description' :
                $rss = $feed->rss;
                $rss[\request('type')] = $content;
                // This does NOT fire updated event :(
                $feed->whereUsername(auth()->user()->username)
                    ->whereFeedId($id)
                    ->update(['rss' => $rss]);
                // This should normally be fired in boot method in Feed model
                event(new FeedUpdateEvent($feed->username, $feed->feed_id));
                break;
            case 'subtitle' :
                $itunes = $feed->itunes;
                $itunes[\request('type')] = $content;
                $feed->whereUsername(auth()->user()->username)
                    ->whereFeedId($id)
                    ->update(['itunes' => $itunes]);
                event(new FeedUpdateEvent($feed->username, $feed->feed_id));
                break;
            case 'showTitle' :
                $guid = \request('guid');
                $entries = $feed->entries;

                foreach($entries as $key => &$entry) {
                    if ($entry['guid'] == $guid) {
                        $entry['title'] = strip_tags($content);
                        break;
                    }
                }
                $feed->whereUsername(auth()->user()->username)
                    ->whereFeedId($id)
                    ->update(['entries' => array_values($entries)]);
                event(new FeedUpdateEvent($feed->username, $feed->feed_id));
                SyncShowToBlog::dispatch($feed, $guid);
                break;
            case 'logo' :
                // Set image as feed logo
                if (Feed::setLogo($feed->username, $feed->feed_id, $content)) {
                    event(new FeedUpdateEvent($feed->username, $feed->feed_id));
                }
                break;
            case 'rss' :
                if ($content === 'set-redirect') {
                    $this->validate($request, [
                        'guid' => 'required|url'
                    ], [], [
                        'guid' => trans('feeds.text_label_redirect_goal'),
                    ]);

                    $domain = $feed->domain;
                    $domain['feed_redirect'] = \request('guid');
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                    }
                } elseif ($content === 'remove-redirect') {
                    $domain = $feed->domain;
                    unset($domain['feed_redirect']);
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                        event(new FeedUpdateEvent($feed->username, $feed->feed_id));
                    }
                }
                break;
            case 'blog' :
                if ($content === 'set-redirect') {
                    $this->validate($request, [
                        'guid' => 'required|url'
                    ], [], [
                        'guid' => trans('feeds.text_label_redirect_goal'),
                    ]);
                    $domain = $feed->domain;
                    $domain['website_redirect'] = \request('guid');
                    $domain['website_type'] = Feed::WEBSITE_TYPE_REDIRECT;
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                    }
                } elseif ($content === 'remove-redirect') {
                    $domain = $feed->domain;
                    unset($domain['website_type']);
                    unset($domain['website_redirect']);
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                    }
                } elseif ($content === 'add-blog') {
                    $domain = $feed->domain;
                    $domain['website_type'] = Feed::WEBSITE_TYPE_WORDPRESS;
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\CreateBlog::dispatch(auth()->user(), $feed->feed_id, $feed->domain);
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                    }
                } elseif ($content === 'unset-blog') {
                    $domain = $feed->domain;
                    $domain['website_type'] = Feed::WEBSITE_TYPE_NONE;

                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                    }
                } elseif ($content === 'remove-blog') {
                    $domain = $feed->domain;
                    $domain['website_type'] = Feed::WEBSITE_TYPE_NONE;
                    if ($feed->whereUsername(auth()->user()->username)
                        ->whereFeedId($id)
                        ->update(['domain' => $domain])) {
                        \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
                        // TODO: Prüfen, ob das Blog nur noch hier verwendet wird und dann Löschen-Job schicken
                        $feeds = Feed::owner()->whereNot('feed_id', '=', $id)->get();

                        foreach ($feeds as $_feed) {
                            if ($_feed['domain']['website_type'] !== false
                                && $_feed['domain']['website_redirect'] !== false
                                && $_feed['domain']['hostname'] == $feed['domain']['hostname']) {
                                break;
                            }
                        }

                        \App\Jobs\DeleteBlog::dispatch(auth()->user(), $feed->feed_id, $feed->domain, 0);
                        break;
                    }
                }
                break;
        }

        $type = trans_choice('feeds.content_type', \request('type'));

        switch ($content) {
            case 'set-redirect';
                $msg = trans('feeds.message_success_set_redirect', ['type' => $type]);
                break;
            case 'remove-redirect';
                $msg = trans('feeds.message_success_redirect_removed', ['type' => $type]);
                break;
            case 'add-blog':
                $msg = trans('feeds.message_success_add_blog');
                break;
            case 'unset-blog':
                $msg = trans('feeds.message_success_unset_blog');
                break;
            case 'remove-blog':
                $msg = trans('feeds.message_success_remove_blog');
                break;
            default:
                $msg = trans('feeds.success_content_updated', ['type' => $type]);
        }

        return response()->json($msg);
    }

    /**
     * Delete podcast
     *
     * *Caution* Removes a podcast.
     *
     * @group Podcasts
     *
     * @param  Feed  $feed
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Feed $feed)
    {
        $title = $feed->rss['title'];
        $msg = trans('feeds.message_error_delete_channel', ['feed' => $title]);

        if ($feed->whereUsername(auth()->user()->username)->whereFeedId($feed->feed_id)->delete()) {
            $msg = trans('feeds.message_success_delete_channel', ['feed' => $title]);
            event(new FeedRemovalEvent($feed));
        }

        return response()->json($msg);
    }

    public function wizard()
    {
        $this->seo()
            ->setTitle(trans('feeds.page_title_feed_wizard'));

        $cFeed = get_package_feature_feeds(auth()->user()->package, auth()->user());
/*        $_localDomains = config('app.local_domains');
        $localDomains = [];
        foreach($_localDomains as $domain) {

        }*/
        $hideNav = false;
        // Hide navigation in onboarding process
        if (\request()->has('h')) {
            $hideNav = true;
        }

        return view('feeds.wizard', compact('cFeed', 'hideNav'/*, 'localDomains'*/));
    }

    public function ppp($id, $feed)
    {
        $user = User::select(['username'])->findOrFail($id);
        $feed = Feed::whereUsername($user->username)->whereFeedId($feed)->firstOrFail();

        //$ppcr = new PodcasterPlayerConfigResource($feed);

        $std = new \stdClass();
        $std->options = [
            "theme" => "default",
            "startPanels" => [
                "Share",
            ]
            //"sslProxy" => "https://cdn.podigee.com/ssl-proxy/",
        ];
        $extensions = new \stdClass();
        $std->extensions = $extensions;

        $podcast = new \stdClass();
        //$podcast->feed = $this->getFeedLink($feed);
        //$podcast->episodes = [];
        $std->podcast = $podcast;

        $episode = new \stdClass();
        $episodeMedia = new \stdClass();
        $episodeMedia->mp3 = 'https://beispiel.podcaster.de/download/podcast_erklaerung_gesprochener_dialog.mp3';
        //'mp3' => 'https://cdn.podigee.com/media/podcast_514_research_vr_podcast_the_science_design_of_virtual_reality_episode_99_rec_room_s_origins_and_its_ugc_future_shawn_whiting_community_designer_against_gr.mp3?v=1560427875'
        //'mp3' => 'https://jam.podcaster.de/download/podcastjam005.mp3'        $episode->media = [
        $episode->media = $episodeMedia;
        $episode->coverUrl = 'https://images.podigee.com/0x,sXqAjUMY77Mw9TK-btc0Mopk5dV7Cw-S3ICDywJz3k_0=/https://cdn.podigee.com/uploads/u493/researchvr%20redesign1466094096e6a5.png';
        $episode->url = "https://researchvr.podigee.io/99-researchvr95";
        $episode->title = "Rec Room's Origins and its UGC Future (Shawn Whiting, Community Designer @ Against Gravity) - 95";
        $episode->subtitle = "The Science and Design behind Virtual Reality";
        $episode->description = "Shawn Whiting, the Community Designer at Against Gravity joins Petr and Azad to discuss the origins and the future of one of the most popular games in VR: Rec Room.";
        $episode->chaptermarks = [];
        $std->episode = $episode;

/*            'coverUrl' => 'https://jam.podcaster.de/download/3000x3000.png',
            'url' => $this->getFeedLink($feed),
            'title' => 'Titel',
            'subtitle' => 'Untertitel',
            'description' => 'Beschreibung',*/

        return response()->json($std, 200, [], JSON_UNESCAPED_SLASHES); // ::withoutWrapping()
    }

    protected function getFeedLink($feed): string
    {
        if (!isset($feed->domain['feed_redirect']) || $feed->domain['feed_redirect'] === false) {
            $podcast = get_feed_uri($feed->feed_id, $feed->domain);
        } else {
            $podcast = $feed->domain['feed_redirect'];
        }

        return $podcast;
    }

    protected function shows()
    {
        event(new FeedUpdateEvent(auth()->user()->username, \request('feedId')));
        event(new ShowAddEvent(auth()->user()->username, \request('feedId'), \request('guid')));
    }

    public function count()
    {
        return response()->json(get_package_feature_feeds(auth()->user()->package, auth()->user()));
    }

    public function domains()
    {
        $feedId = \request('feedId');
        $type = \request('type', 'local');
        $feed = Feed::owner()
            ->whereFeedId($feedId)
            ->select(['feed_id', 'rss.title', 'domain'])
            ->firstOrFail();
        $feeds = Feed::owner()
            ->where('feed_id', '!=', $feedId)
            ->where('domain.hostname', '!=', $feed->domain['hostname'])
            ->when(($type === 'local'), function ($query) {
                return $query->whereNotIn('domain.is_custom', [1, '1', true]);
            })
            ->when(($type === 'custom'), function ($query) {
                return $query->whereIn('domain.is_custom', [1, '1', true]);
            })
            ->select(['domain'])
            ->get();

        // Make sure default domain is always returned if list of non-custom domains is requested

        $mapped = $feeds->map(function($item) use ($type) {

            $domain = $item->domain;
            $domain['stripped_subdomain'] = $this->getStrippedDomain($domain);

            if ($domain['is_custom']) {
                $domain['domain'] = $domain['tld'];
            }
            $domain['name'] = $item->domain['subdomain'] . '.' . $item->domain['tld'];

            return $domain;
        });

        if ($type === 'local') {
            $defaultDomain = get_default_domain(auth()->user()->username);
            $defaultDomain['name'] = $defaultDomain['subdomain'] . '.' . $defaultDomain['tld'];
            $defaultDomain['stripped_subdomain'] = $this->getStrippedDomain($defaultDomain);
            $mapped->push(collect($defaultDomain));
        }

        $userdomains = $mapped->unique('domain.hostname');

        $tlds = $feeds->map(function($item) {
            $tld = $item->domain['is_custom'] ? $item->domain['tld'] : $item->domain['domain'];

            return [
                'value' => $tld,
                'text' => '.' . $tld,
            ];
        });

        $hostdomains = collect(config('app.local_domains'))->map(function ($item) {
            return [
                'value' => $item,
                'text' => '.' . $item,
            ];
        });

        return response()->json([
            'feeds' => $userdomains->toArray(),
            'tlds' => $tlds,
            'hostdomains' => $hostdomains,
        ]);
    }

    public function tlds()
    {
        $aTLDs = get_tlds();
        array_walk($aTLDs, function(&$val) { $val = ['value' => $val, 'text' => '.' . $val]; } );

        return response()->json($aTLDs);
    }

    public function check(Request $request)
    {
        $this->validate($request, [
            'domain' => 'required',
            'tld' => 'required',
            'is_custom' => 'required|boolean',
        ]);
        $domain = \request('domain');
        $tld = \request('tld');
        $dm = new DomainManager();

        if (\request('is_custom')) {
            $dm->isValidCustomDomain($domain, $tld);
        } else {
            $dm->isValidLocalDomain(auth()->user(), $domain, $tld);
        }

        return response()->json([
            'message' => 'checkPassed',
        ]);
    }

    public function getChanges(Request $request)
    {
        $validated = $this->validate($request, [
            'feedId' => 'required',
            'domain' => 'required',
            'tld' => 'required',
            'is_custom' => 'required|boolean',
        ]);

        $feed = Feed::owner()
            ->whereFeedId($validated['feedId'])
            ->select(['feed_id', 'rss.title', 'domain'])
            ->get();

        $feeds = Feed::owner()
            ->where('domain.hostname', '=', $feed[0]->domain['hostname'])
            ->where('feed_id', '!=', $validated['feedId'])
            ->select(['feed_id', 'rss.title', 'domain'])
            ->get();

        $newDomain = get_default_domain(auth()->user()->username);
        $newDomain['tld'] = $validated['tld'];
        $newDomain['subdomain'] = $validated['domain'];
        $newDomain['hostname'] = 'https://' . $validated['domain'] . '.' . $validated['tld'];
        $newDomain['domain'] = $validated['domain'] . '.' . $validated['tld'];

        $feedsWithSharedDomain = $feeds->filter(function ($item) {
            return (isset($item->domain['website_type']) && $item->domain['website_type']
                && !isset($item->domain['website_redirect']) || isset($item->domain['website_redirect']) && !$item->domain['website_redirect']);
        })->map(function ($item) {
            return $item->rss['title'];
        });

        $result = $feed->map(function ($item) use ($newDomain, $feedsWithSharedDomain) {

           $a = [
               'title' => $item->rss['title'],
               'feed' => [
                   'old' => get_feed_uri($item->feed_id, $item->domain),
                   'new' => get_feed_uri($item->feed_id, $newDomain),
               ],
           ];

           if (!$feedsWithSharedDomain || $feedsWithSharedDomain->count() < 1) {
               $a['website'] = [
                   'old' => get_blog_uri($item->domain),
                   'new' => get_blog_uri($newDomain),
               ];
           } else {
               $a['website'] = [
                   'old' => get_blog_uri($item->domain),
                   'new' => trans('feeds.hint_create_new_blog_collision', ['channels' => implode(', ', $feedsWithSharedDomain->toArray())]),
               ];
           }

           return $a;
        });

        return response()->json($result);
    }

    public function saveChanges(Request $request)
    {
        $validated = $this->validate($request, [
            'feed_id' => 'required',
            'domain' => 'required',
            'tld' => 'required',
            'is_custom' => 'required|boolean',
        ]);
        $domain = $validated['domain'];
        $tld = $validated['tld'];
        $user =auth()->user();
        $username = $user->username;

        $dm = new DomainManager();

        if (\request('is_custom')) {
            $dm->isValidCustomDomain($domain, $tld);
        } else {
            $dm->isValidLocalDomain($user, $domain, $tld);
        }

        $feedId =  $validated['feed_id'];
        $feed = Feed::owner()->whereFeedId($feedId)->firstOrFail();
        $feeds = Feed::owner()
            ->where('domain.hostname', '=', $feed->domain['hostname'])
            ->where('feed_id', '!=', $feedId)
            ->select(['feed_id', 'rss.title', 'domain'])
            ->get();

        $oldDomain = $feed->domain;

        $newDomain = get_default_domain($username);
        $newDomain['tld'] = $validated['tld'];
        $newDomain['subdomain'] = $validated['domain'];
        $newDomain['hostname'] = 'https://' . $validated['domain'] . '.' . $validated['tld'];
        $newDomain['domain'] = $validated['domain'] . '.' . $validated['tld'];

        // Get count of other feeds that use the same base url
        $countFeedsWithSharedDomain = $feeds->filter(function ($item) {
            return (isset($item->domain['website_type'])
                && $item->domain['website_type']
                && !isset($item->domain['website_redirect'])
                ||
                isset($item->domain['website_redirect'])
                && !$item->domain['website_redirect']);
        })->count();

        // Feed uses blog but not redirect
        if (isset($feed->domain['website_type'])
            && $feed->domain['website_type']
            && !isset($feed->domain['website_redirect'])
            ||
            isset($feed->domain['website_redirect'])
            && !$feed->domain['website_redirect']) {
                // Other feeds share the same blog
                if ($countFeedsWithSharedDomain < 1) {
                    // If there are no other feeds which use the same base url
                    // use the old setting otherwise the user has to create a new blog
                    $newDomain['website_type'] = $feed->domain['website_type'];
                }
        } else {
            $newDomain['website_redirect'] = $feed->domain['website_redirect'];
        }

        if (!$feed->whereUsername($username)
            ->whereFeedId($feedId)
            ->update(['domain' => $newDomain])) {
            throw new \Exception(trans('feeds.exception_domain_not_updated'));
        }

        if (!isset($newDomain['website_type'])
        || !$newDomain['website_type']
        || $newDomain['website_type'] == Feed::WEBSITE_TYPE_NONE
        && $countFeedsWithSharedDomain < 1) {
            // If there is no need for the blog itself anymore
            // here or anywhere else delete it completely
            DeleteBlog::dispatch($user, $feedId, $oldDomain);
        }

        // Blog was used before but since domain name changed
        // we have to update the domain name of the blog
        // but we only want to do this if the blog is only used here
        if ($countFeedsWithSharedDomain < 1) {
            if (isset($oldDomain['website_type'])
                && $oldDomain['website_type'] == Feed::WEBSITE_TYPE_WORDPRESS) {
                UpdateBlogDomain::dispatch($user, $newDomain, $oldDomain);
            }
        } else {
            // Otherwise create new blog for this domain
            if ($newDomain['website_type'] == Feed::WEBSITE_TYPE_WORDPRESS) {
                CreateBlog::dispatch($user, $feedId, $newDomain);
                // Remove feed entry in old blog
                DeleteBlogFeed::dispatch($username, $feedId, $oldDomain);
            }
        }

        (new NginxServerConfigWriter($username, "{$oldDomain['subdomain']}.{$oldDomain['tld']}"))->delete();

        event(new FeedUpdateEvent($username, $feedId));

        $this->dispatch(new \App\Jobs\WriteWebserverConfig(auth()->user(), $feed->feed_id));

        return response()->json([
            'message' => trans('feeds.message_success_change_base_url'),
        ]);
    }


    /**
     * @param  array  $domain
     * @return string
     */
    private function getStrippedDomain(array $domain): string
    {
        if ($domain['is_custom']) {
            return $domain['subdomain'];
        }

        return Str::before($domain['subdomain'] . '.' . $domain['tld'], '.' . $domain['domain']);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function adoptin()
    {
        $cnt = Feed::where('settings.ads', '1')->count();

        return response()->json($cnt);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return response()->redirectToRoute('podcast.wizard');

        //return view('feeds.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {

    }
}
