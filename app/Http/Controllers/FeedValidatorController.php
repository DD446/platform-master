<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Classes\FeedValidator;
use App\Jobs\ValidateFeed;
use App\Models\Feed;
use SEO;

class FeedValidatorController extends Controller
{
    public function index(Request $request, $id)
    {
        $feed = Feed::owner()->findOrFail($id);

        if (request()->wantsJson()) {

            if (!isset($feed->state)) {
                $res['state'] = 'success';
                $res['stateLogo'] = 'success';

                foreach (['Channel', 'Logo', 'Show', 'Enclosure'] as $type) {
                    $class = "App\Classes\FeedValidator\\".$type;
                    /** @var FeedValidator $class */
                    $o = new $class(auth()->user()->username, $id);
                    $res['message'][$type] = $o->run();

                    if (count($res['message'][$type]['errors']) > 0) {
                        $res['state'] = 'danger';

                        if ($type === 'Logo') {
                            $res['stateLogo'] = 'danger';
                        }
                    } else if (count($res['message'][$type]['warnings']) > 0) {
                        $res['state'] = ['warning'];

                        if ($type === 'Logo') {
                            $res['stateLogo'] = 'warning';
                        }
                    }
                }
            } else {
                $res = $feed->state;
            }
            return response()->json($res);
        }

/*        SEO::setTitle(trans('feeds.title_validator', ['feed' => $feed->rss['title'] ?? $feed->feed_id]));

        $uuid = user_uuid(auth()->user());

        return view('feeds.state', compact('feed', 'uuid'));*/
        return redirect()->to(route('feeds') . '#/podcast/' . $feed->feed_id . '/status');
    }

    public function actions(Request $request, $id, $type)
    {
        $prefix = "App\Classes\FeedValidator\\";
        $class = $prefix . $type;
        /** @var FeedValidator $class */
        $o = new $class(auth()->user()->username, $id);

        return response()->json($o->getCheckMethods());
    }

    public function run(Request $request)
    {
        $job = (new ValidateFeed(auth()->user(), request('feed'), request('type'), request('uuid')))
            ->delay(Carbon::now()->addSeconds(1));

        $res = dispatch($job);

        return response()->json($res);
    }
}
