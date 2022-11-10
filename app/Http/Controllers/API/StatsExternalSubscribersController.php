<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StatsExternalSubscribersRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExternalSubscriber;

class StatsExternalSubscribersController extends Controller
{
    /**
     * External subscribers
     *
     * Get subscriber counts for various podcast services.
     *
     * @group Statistics
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function index(StatsExternalSubscribersRequest $request)
    {
        // TODO: Cacheing
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

        return response()->json($merged);
    }
}
