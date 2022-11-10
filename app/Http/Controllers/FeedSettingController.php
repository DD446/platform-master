<?php

namespace App\Http\Controllers;

use App\Events\FeedUpdateEvent;
use axy\htpasswd\PasswordFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Classes\TrackingManager;
use App\Models\Feed;
use App\Models\Package;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FeedSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index($feed)
    {
        $oFeed = Feed::owner()->findOrFail($feed);

        if (request()->wantsJson()) {
            $settings = $oFeed->settings;
            $settings['can'] = [
                'ads' => has_package_feature(auth()->user()->package, Package::FEATURE_ADS),
                'ads_custom' => has_package_feature(auth()->user()->package, Package::FEATURE_ADS_CUSTOM),
                'auphonic' => has_package_feature(auth()->user()->package, Package::FEATURE_AUPHONIC),
                'socialmedia' => has_package_feature(auth()->user()->package, Package::FEATURE_SOCIALMEDIA),
                'protection' => has_package_feature(auth()->user()->package, Package::FEATURE_PROTECTION),
            ];

            if (!isset($settings['downloadlink_description']) || !$settings['downloadlink_description']) {
                $settings['downloadlink_description'] = trans('feeds.default_download_link_entry');
            }
            if (!isset($settings['feed_entries']) || !$settings['feed_entries']) {
                $settings['feed_entries'] = 50;
            }
            if (!isset($settings['ping'])) {
                $settings['ping'] = 1;
            }
            if (!isset($settings['styled_feed'])) {
                $settings['styled_feed'] = 1;
            }
            return response()->json($settings);
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function show(Feed $feed, $setting)
    {
        if (\request()->isMethod('HEAD')) {
            if ($setting === 'domain') {
                if (\request()->has('check') && \request('check') == 'isCustom') {
                    $status = 200;

                    if (!$feed->domain['is_custom'] && Str::before($feed->domain['subdomain'], '.') == $feed->username) {
                        $status = 201;
                    }

                    return response(null, $status);
                }
                return response(null, 201);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function edit(Feed $feed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Feed $feed
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(Request $request, $feed, $setting)
    {
        $feed = Feed::whereFeedId($feed)->whereUsername(auth()->user()->username)->firstOrFail();
        $settings = $feed->settings;
        $settings = array_merge([
            'inactiveComments' => 0,
            'ping' => 0,
            'feed_entries' => 100,
        ], $settings ?? []);

        $save = true;
        $renewWebserverConfig = false;
        $code = 200;

        switch ($setting) {
            case 'ads':
                $ads = $request->data;
                $settings['ads'] = $ads;
                $msg = ['message' => trans_choice('feeds.success_ads_setting_changed', $ads)];
                break;
            case 'audiotakes':
                $at = $request->data;
                $settings['audiotakes'] = $at['state'];
                $settings['audiotakes_id'] = $at['id'];

                if ($settings['audiotakes'] == 1) {
                    try {
                        // Check that the redirect actually works
                        $enclosureLink = $this->getEnclosureLink($feed);
                        $aUrl = parse_url($enclosureLink);
                        $file = pathinfo($aUrl['path'], PATHINFO_FILENAME);
                        $ext = pathinfo($aUrl['path'], PATHINFO_EXTENSION);
                        $link = 'https://deliver.audiotakes.net/d/'. $aUrl['host'] . '/p/' . $feed->feed_id . '/m/' . $file . '.' . $ext . '?awCollectionId=' . ($settings['audiotakes_id'] ?? 'AT00000');

                        Log::debug("Audiotakes check: Testing link : " . $link . " and enclosure link: " . $enclosureLink);

                        if ($link) {
                            (new TrackingManager())->checkAudiotakes($link, $enclosureLink);
                        }

                        $settings['chartable'] = $settings['rms'] = $settings['podcorn'] = $settings['podtrac'] = 0;
                        $msg = ['message' => trans('feeds.success_setting_saved', ['service' => 'Podcast Pioniere'])];
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        Log::error("Audiotakes check: Failed with message: " . $message);
                        $msg = ['message' => trans('feeds.error_saving_setting_failed', ['detail' => $message])];
                        $code = $e->getCode() < 400 ? 500 : $e->getCode();
                        $save = false;
                    }
                } else {
                    $msg = ['message' => trans('feeds.success_setting_disabled', ['service' => 'Podcast Pioniere'])];
                }
                break;
            case 'chartable':
                $aChartable = $request->data;
                $settings['chartable'] = $aChartable['state'];

                if ($settings['chartable'] == 1) {
                    // Check that the redirect actually works
                    $enclosureLink = $this->getEnclosureLink($feed);
                    $link = 'https://chtbl.com/track/' . $aChartable['id'] . '/' . $enclosureLink;

                    try {
                        (new TrackingManager())->checkChartable($link, $enclosureLink);

                        $settings['audiotakes'] = $settings['rms'] = $settings['podcorn'] = $settings['podtrac'] = 0;
                        $msg = ['message' => trans('feeds.success_setting_saved', ['service' => 'Chartable'])];
                    } catch (\Exception $e) {
                        $message = $e->getMessage() ?: trans('feeds.text_check_chartable_id');
                        $msg = ['message' => trans('feeds.error_saving_setting_failed', ['detail' => $message])];
                        $code = $e->getCode() < 400 ? 500 : $e->getCode();
                        $save = false;
                    }
                } else {
                    $msg = ['message' => trans('feeds.success_setting_disabled', ['service' => 'Chartable'])];
                }
                $settings['chartable_id'] = $aChartable['id'];
                break;
            case 'rms':
                $aRms = $request->data;
                $settings['rms'] = $aRms['state'];
                $settings['rms_id'] = $aRms['id'];

                if ($settings['rms'] == 1) {

                    // https://rmsi-podcast.de/comedyperiode/media/42_Folge.mp3?awCollectionId=STO0001
                    $path = parse_url($this->getEnclosureLink($feed),  PHP_URL_PATH);
                    $link = 'https://rmsi-podcast.de' . $path . '?awCollectionId=' . $settings['rms_id'];

                    try {
                        (new TrackingManager())->checkRms($link);

                        $settings['audiotakes'] = $settings['chartable'] = $settings['podcorn'] = $settings['podtrac'] = 0;
                        $msg = ['message' => trans('feeds.success_setting_saved', ['service' => 'RMS'])];
                    } catch (\Exception $e) {
                        $message = $e->getMessage() ?: trans('feeds.text_check_rms');
                        $msg = ['message' => trans('feeds.error_saving_setting_failed', ['detail' => $message])];
                        $code = $e->getCode() < 400 ? 500 : $e->getCode();
                        $save = false;
                    }
                } else {
                    $msg = ['message' => trans('feeds.success_setting_disabled', ['service' => 'RMS'])];
                }
                break;
            case 'podcorn':
                // https://pdcn.co/e/
                $settings['podcorn'] = $request->data;

                if ($settings['podcorn'] == 1) {
                    $settings['audiotakes'] = $settings['chartable'] = $settings['rms'] = $settings['podtrac'] = 0;
                    $msg = ['message' => trans('feeds.success_setting_saved', ['service' => 'Podcorn'])];
                } else {
                    $msg = ['message' => trans('feeds.success_setting_disabled', ['service' => 'Podcorn'])];
                }
                break;
            case 'podtrac':
                // http://dts.podtrac.com/redirect.mp3/www.podhost.org/media/audiofile.mp3
                // http://dts.podtrac.com/redirect.aac/www.oldies104.net/podcast/media/rockhouse_2018_06_14.aac

                $settings['podtrac'] = $request->data;

                if ($settings['podtrac'] == 1) {
                    try {
                        $aUrl = parse_url($this->getEnclosureLink($feed));
                        $ext = File::extension($aUrl['path']);
                        $link = 'https://dts.podtrac.com/redirect.' . $ext . '/' . $aUrl['host'] . $aUrl['path'];

                        (new TrackingManager())->checkPodtrac($link);

                        $settings['audiotakes'] = $settings['chartable'] = $settings['rms'] = $settings['podcorn'] = 0;
                        $msg = ['message' => trans('feeds.success_setting_saved', ['service' => 'Podtrac'])];
                    } catch (\Exception $e) {
                        $message = $e->getMessage() ?: trans('feeds.text_check_podtrac');
                        $msg = ['message' => trans('feeds.error_saving_setting_failed', ['detail' => $message])];
                        $code = $e->getCode() < 400 ? 500 : $e->getCode();
                        $save = false;
                    }
                } else {
                    $msg = ['message' => trans('feeds.success_setting_disabled', ['service' => 'Podtrac'])];
                }
                break;
            case 'defaults':
                $validated = $this->validate($request, [
                    'default_item_title' => 'nullable|string|max:255',
                    'default_item_description' => 'nullable|string|max:4000',
                    'downloadlink' => 'nullable|in:0,1',
                    'downloadlink_description' => 'nullable|string|max:4000',
                    'inactiveComments' => 'nullable|in:0,1',
                    'ping' => 'nullable|in:0,1',
                    'styled_feed' => 'nullable|in:0,1',
                    'feed_entries' => ['numeric', 'min:-1', 'max:5000'],
                ]);

                if (isset($validated['styled_feed']) && $validated['styled_feed'] == 1) {
                    // Update nginx config file when styled feeds
                    // TODO: Maybe only create custom config if custom domain (TLD or subdomain != username) is used
                    $renewWebserverConfig = true;
                }

                $settings = array_merge($settings, $validated);
                $msg = ['message' => trans('feeds.success_setting_defaults_saved')];
                break;
            case 'approvals':
                $validated = $this->validate($request, [
                    'approvals' => 'nullable|array',
                    //'approvals.*' => 'nullable|array',
                ], [], [
                    'approvals' => trans('feeds.validation_attribute_approvals')
                ]);

                $settings = array_merge($settings, $validated);
                $msg = ['message' => trans('feeds.success_setting_approvals_saved')];
                break;
            case 'privacy':
                $validated = $this->validate($request, [
                    'authname' => 'required|string|alpha_num',
                    'password' => 'required|string|different:authname',
                    'confirmed' => 'accepted',
                ], [], [
                    'authname' => trans('feeds.validation_attribute_authname'),
                    'confirmed' => trans('feeds.validation_attribute_confirmed'),
                ]);

                $file = new PasswordFile(storage_path('hostingstorage/conf/') . "{$feed->domain['subdomain']}.{$feed->domain['tld']}.htpasswd");
                $file->setPassword($validated['authname'], $validated['password']);
                $file->save();

                $settings = array_merge($settings, ['styled_feed' => 1, 'uses_protection' => "yes", 'auth_names' => [$validated['authname']]]);
                $msg = ['message' => trans('feeds.success_setting_privacy_saved')];
                $renewWebserverConfig = true;
                break;
            default:
                throw new \Exception("Unknown setting");
        }

        if ($save && !$feed->whereFeedId($feed->feed_id)->whereUsername($feed->username)->update(['settings' => $settings])) {
            $msg = ['message' => trans('feeds.error_setting_changed')];
            $code = 500;
        } else {
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));

            if ($renewWebserverConfig) {
                \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
            }
        }

        return response()->json($msg)->setStatusCode($code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function destroy(Feed $feed, $setting)
    {
        $save = false;
        $renewWebserverConfig = false;
        $code = 200;
        $settings = $feed->settings;

        switch ($setting) {
            case 'protection':
                $save = true;
                $renewWebserverConfig = true;
                unset($settings['uses_protection']);
                unset($settings['auth_names']);
                File::delete(storage_path('hostingstorage/conf/') . "{$feed->domain['subdomain']}.{$feed->domain['tld']}.htpasswd");
                $msg = ['message' => trans('feeds.success_setting_protection_removed')];
                break;
            default:
                throw new \Exception("Unknown type");
        }

        if ($save && !$feed->whereFeedId($feed->feed_id)->whereUsername($feed->username)->update(['settings' => $settings])) {
            $msg = ['message' => trans('feeds.error_setting_changed')];
            $code = 500;
        } else {
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));

            if ($renewWebserverConfig) {
                \App\Jobs\WriteWebserverConfig::dispatch(auth()->user(), $feed->feed_id);
            }
        }

        return response()->json($msg)->setStatusCode($code);
    }

    /**
     * @param  Feed  $feed
     * @return string
     * @throws \Exception
     */
    private function getEnclosureLink(Feed $feed)
    {
        $show = get_newest_show($feed, true);

        if (!$show) {
            throw new \Exception(trans('feeds.exception_no_show_found'), 404);
        }

        $aFile = get_file($feed->username, $show['show_media']);

        if (!$aFile) {
            throw new \Exception(trans('feeds.exception_file_not_found'), 404);
        }

        return get_enclosure_uri($feed->feed_id, $aFile['name'], $feed->domain);
    }
}
