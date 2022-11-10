<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Classes\FeedSubmitter;
use App\Classes\PodcastLinkService;
use App\Models\Feed;
use SEO;

class FeedSubmitController extends Controller
{
    const SERVICES = ['itunes', 'spotify', 'podcast', 'google', 'deezer', 'samsung', 'tunein', 'stitcher', 'playerfm', 'listennotes', 'castbox', 'pocketcasts', 'fyyd', 'amazon', 'podimo', /*'fyeo', 'audionow', 'castro', 'gpodder'*/];

    /**
     * @var PodcastLinkService
     */
    private $podcastLinkService;

    /**
     * FeedSubmitController constructor.
     */
    public function __construct(PodcastLinkService $podcastLinkService)
    {
        $this->podcastLinkService = $podcastLinkService;
    }

    public function index(Request $request, $id)
    {
        $feed = Feed::owner()->findOrFail($id);

/*        SEO::setTitle(trans('feeds.title_submitter', ['feed' => $feed->rss['title'] ?? $feed->feed_id]));

        $uuid = user_uuid(auth()->user());

        return view('feeds.submit', compact('feed', 'uuid'));*/

        return redirect()->to(route('feeds') . '#/podcast/' . $feed->feed_id . '/promotion');
    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(self::SERVICES),
            ],
            'feed' => 'required',
        ]);

        $feed = Feed::owner()->findOrFail(request('feed'));
        $class = "App\Classes\FeedSubmitter\\" . ucfirst(Str::camel(request('type')));
        /** @var FeedSubmitter $class */
        $o = new $class($feed);

        $res = $o->check();

        return response()->json([
            'submitted' => $res,
            'link' => $o->getLink(),
            'submit' => $o->submit(),
            'isForm' => $o->isForm(),
            'canValidate' => $o->canValidate(),
            'placeholderLink' => $o->getPlaceholderLink(),
            'helpLink' => $o->getHelpLink(),
        ]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(self::SERVICES),
            ],
            'feed' => 'required',
            'link' => 'required|url',
        ]);

        $msg = ['message' => trans('feeds.success_submit_link_added')];
        $feed = Feed::owner()->findOrFail(request('feed'));

        if (!$this->podcastLinkService->save($feed, request('type'), request('link'))) {
            //$msg = ['error' => trans('feeds.error_submit_link_added')];
            throw new \Exception(trans('feeds.error_submit_link_added'));
        }

        return response()->json($msg);
    }

    public function destroy($type, $feed)
    {
        $oFeed = Feed::owner()->findOrFail($feed);
        $msg = ['message' => trans('feeds.success_submit_link_deleted')];

        if (!$this->podcastLinkService->save($oFeed, $type)) {
            throw new \Exception(trans('feeds.error_submit_link_deleted'));
        }

        return response()->json($msg);
    }
}
