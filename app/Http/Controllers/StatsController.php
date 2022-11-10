<?php

namespace App\Http\Controllers;

use App\Models\AudiotakesContract;
use Artesaos\SEOTools\Traits\SEOTools;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use JamesHeinrich\GetID3\GetID3;
use Khill\Duration\Duration;
use App\Classes\Statistics;
use App\Models\Download;
use App\Models\ExternalSubscriber;
use App\Models\Feed;
use App\Models\Package;
use App\Scopes\IsVisibleScope;

class StatsController extends Controller
{
    use SEOTools;

    public function index()
    {
        if (Gate::forUser(auth()->user())->denies('viewStats')) {
            abort(403);
        }

        $this->seo()
            ->setTitle(trans('stats.page_title'));

        $usesAudiotakes = AudiotakesContract::owner()->count();
        $canExport = has_package_feature(auth()->user()->package, Package::FEATURE_STATISTICS_EXPORT);
        $useNewStatistics = auth()->user()->use_new_statistics;

        return view('stats.index', compact('usesAudiotakes', 'canExport', 'useNewStatistics'));
    }

    public function storage()
    {
        $userSpace = get_user_space(auth()->user());
        $availableSpace = $userSpace['raw']['available'];
        $maxSpace = $userSpace['raw']['total'];
        $usedSpace = $userSpace['raw']['used'];

        $sizeAvailablePercent = round(($availableSpace/$maxSpace)*100, 2, PHP_ROUND_HALF_DOWN);
        $sizeUsedPercent = round(($usedSpace/$maxSpace)*100, 2, PHP_ROUND_HALF_DOWN);
        $label = $userSpace['available'];

        if($label == '0 B') {
            $label = 'Kein Speicherplatz verfügbar';
            $sizeUsedPercent = 99.99;
            $sizeAvailablePercent = 0.01;
        }

        return response()->json([
            'percentages' => [$sizeUsedPercent, $sizeAvailablePercent],
            'label' => $label,
        ]);
    }

    public function externalSubscribers()
    {
        $yesterday = CarbonImmutable::yesterday()->format('Y-m-d');
        $es = ExternalSubscriber::groupBy('user_agent')->where('date', '=', $yesterday)->get(['user_agent', 'subscribers']);
        //$es = ExternalSubscriber::groupBy('user_agent')->orderBy('created', 'DESC')->get(['user_agent', 'subscribers']);
        $keyed = $es->mapWithKeys(function ($item) {
            $a = [$item['user_agent'] => $item['subscribers']];
            $ua = mb_strtolower($item['user_agent']);
            switch ($ua) {
                case 'podcaster.de':
                    $id = 'podcaster';
                    break;
                case 'podcast.de':
                    $id = 'podcast';
                    break;
                case 'overcast':
                case 'breaker':
                case 'feedbin':
                case 'g2reader':
                case 'bloglovin':
                case 'instacast':
                //case 'mailchimp':
                case 'newsify':
                case 'ucast':
                case 'playerfm':
                    $id = $ua;
                    break;
                default:
                    return false;
            }

            return [$id => $a];
        });

        $defaults = collect(['podcaster' => ['podcaster.de' => '-'], 'podcast' => ['podcast.de' => 0]/*, 'overcast' => ['Overcast' => 0], 'breaker' => ['Breaker' => 0]*/]);
        $merged = $defaults->merge($keyed);

        //return response()->json($merged);

        return view('stats.parts.external_subscribers', ['data' => $merged, 'hideNav' => true]);
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     * @hideFromAPIDocumentation
     */
    public function shows(Request $request, $id)
    {
        $feed = Feed::owner()->findOrFail($id);

        if ($request->is('api/*')) {
            $infos = [
                'average_duration' => '0', // 	ist die durchschnittliche Laufzeit einer Episode.
                'complete_runtime' => '0', // Tage 	ist die vollständige Laufzeit aller Episoden.
                'average_filesize' => '0', // MB 	ist die durchschnittliche Dateigröße der Medien.
                'complete_filesize' => '0', // 9.3 MB 	ist die vollständige Dateigröße der Medien.
                'average_publish_time' => '-', // 0 Tage 	ist der durchschnittliche Zeitraum, bis eine neue Episode veröffentlicht wird.
            ];

            $countShows = $feed->shows->count();
            $duration = new Duration();
            $minDate = time();
            $maxDate = -1;
            $getID3 = new GetID3();
            $shows = $feed->shows;

            foreach($shows as $show) {
                if (isset($show->show_media) && !empty($show->show_media)) {
                    try {
                        $file = get_file($feed->username, $show->show_media);

                        if ($file) {
                            $infos['complete_filesize'] += $file['byte'];
                        }

                        if (isset($show->itunes['duration'])
                            && $show->itunes['duration']
                            // Fixes microsecond bug
                            && strpos($show->itunes['duration'], '.') === false) {
                            $infos['complete_runtime'] += $duration->toSeconds($show->itunes['duration']);
                        } else {
                            // If duration is not available
                            // extract it and save it to data storage
                            if ($file) {
                                // HEAVY OPERATION
                                $mixinfo = $getID3->analyze($file['path']);
                                $showDuration = $mixinfo['playtime_seconds'] ?? 0;
                                $infos['complete_runtime'] += $showDuration;
                                $itunes = $show->itunes;
                                $itunes['duration'] = get_duration($feed->username, $show->show_media);
                                $show->itunes = $itunes;
                                $show->save();
                            }
                        }

                        if ($show->lastUpdate < $minDate) {
                            $minDate = $show->lastUpdate;

                            if ($maxDate === -1) {
                                $maxDate = $show->lastUpdate;
                            }
                        }

                        if ($show->lastUpdate > $maxDate) {
                            $maxDate = $show->lastUpdate;
                        }
                    } catch (\Exception $e) {
                    }
                }
            }

            $infos['average_filesize'] = $countShows > 0 ? get_size_readable($infos['complete_filesize']/$countShows, 2, 1000) : 0;
            $infos['complete_filesize'] = get_size_readable($infos['complete_filesize'], 2, 1000);
            $infos['average_duration'] = $countShows > 0 ? $duration->humanize($infos['complete_runtime'] / $countShows) : 0;
            $infos['complete_runtime'] = $duration->humanize($infos['complete_runtime']);
            $infos['average_publish_time'] = $countShows > 0 ? $duration->humanize(($maxDate-$minDate)/$countShows) : 0;

            return response()->json($infos);
        }

        abort(404);
    }
}
