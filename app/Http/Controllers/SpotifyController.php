<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Feed;
use App\Models\Page;
use App\Models\User;
use podcasthosting\PodcastClientSpotify\Delivery\Client;
use podcasthosting\PodcastClientSpotify\Exceptions\AuthException;
use podcasthosting\PodcastClientSpotify\Exceptions\DomainException;
use podcasthosting\PodcastClientSpotify\Exceptions\DuplicateException;
use podcasthosting\PodcastClientSpotify\Delivery\Result;

class SpotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            $feeds = Feed::owner()
                ->select(['feed_id', 'rss.title', 'domain.feed_redirect', 'settings.spotify_uri', 'settings.spotify_updated'])
                ->get();
            $aFeeds = [];

            foreach ($feeds as $feed) {
                // User cannot submit redirected feeds to Spotify
                if ($feed['domain']['feed_redirect'] === true) {
                    continue;
                }
                $aFeeds[] = [
                    'id' => $feed['feed_id'],
                    'name' => $feed['rss']['title'],
                    'spotify_uri' => $feed['settings']['spotify_uri'] ?? null,
                    'spotify_updated' => $feed['settings']['spotify_updated'] ?? null, // TODO: I18N
                    // TODO: custom domain needs special handling in frontend
                ];
            }

            return response()->json($aFeeds);
        }

        \SEO::setTitle(trans('spotify.page_title'));

        // For background image
        //$page = Page::where('title', '=', 'spotify')->first();
        $page = null;

        return view('spotify.index', compact('page'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->get('id');
        $feed = Feed::owner()
            ->select('feed_id', 'domain')
            ->where('feed_id', '=', $id)
            ->firstOrFail();
        $uri = get_feed_uri($feed['feed_id'], $feed['domain']);
        $dc = new Client(config('services.spotify.token'));
        $code = 200;
        Feed::owner()
            ->where('feed_id', '=', $id)
            ->update(['settings.spotify' => "1"]);

        try {
            $response = $dc->create($id, $uri);
            // For testing:
            // $response = new Result('spotify:show:789');

            if ($response instanceof Result) {
                $uri = $response->getSpotifyUri();

                Feed::owner()
                    ->where('feed_id', '=', $id)
                    ->update(['settings.spotify_uri' => $uri, 'settings.spotify_updated' => today()]);

                $res = [
                    'message' => trans('spotify.success', ['uri' => $uri]),
                    'uri' => $uri,
                ];
            }
        } catch (AuthException $e) {
            $res = trans('spotify.auth_exception');
            $code = 401;
        } catch (DuplicateException $e) {
            $res = trans('spotify.duplicate_exception');

            Feed::owner()
                ->where('feed_id', '=', $id)
                ->whereNull('settings.spotify_updated')
                ->update(['settings.spotify_updated' => today()]);
        } catch (DomainException $e) {
            $res = trans('spotify.domain_exception');
            $code = 500;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $res = trans('spotify.unknown_exception');
            $code = 500;
        }

        return response()->json($res)->setStatusCode($code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::debug("Spotify destroy called for feed id {$id} from user " . auth()->user()->username);
        $feed = Feed::owner()
            ->select('settings.spotify_uri')
            ->where('feed_id', '=', $id)
            ->firstOrFail();
        $dc = new Client(config('services.spotify.token'));
        $res = trans('spotify.deletion_failed');
        Feed::owner()
            ->where('feed_id', '=', $id)
            ->update(['settings.spotify' => "0"]);

        try {
            $response = $dc->remove($feed['settings']['spotify_uri']);

            if ($response === true) {
                $update = Feed::owner()
                    ->where('feed_id', '=', $id)
                    ->update(['settings.spotify_uri' => null, 'settings.spotify_updated' => null]);

                if ($update) {
                    $res = trans('spotify.deletion_success');
                }
            }
        } catch (AuthException $e) {
            $res = trans('spotify.auth_exception');
        } catch (\Exception $e) {
            return response()->json($res)->setStatusCode(500);
        }

        return response()->json($res);
    }

    public function show($spotify)
    {
        if (auth()->user()->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }

        return view('spotify.show', compact('spotify'));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'spotifyUri' => 'required'
        ]);

        if (auth()->user()->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
        $id = request('spotifyUri');

        if (!Str::startsWith($id, 'spotify:show:')) {
            $id = 'spotify:show:' . $id;
        }

        $res = "Eintrag erfolgreich gelöscht.";

        $dc = new Client(config('services.spotify.token'));
        try {
            $response = $dc->remove($id);
            if ($response !== true) {
                $res = "Der Eintrag wurde nicht gelöscht.";
            }
        } catch (AuthException $e) {
            $res = trans('spotify.auth_exception');
        } catch (\Exception $e) {
            $res = "Es ist ein Fehler aufgetreten: " . $e->getMessage();
        }

        return response()->redirectToRoute('spotify.index')->with(['status' => $res]);
    }
}
