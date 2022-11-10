<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Classes\Activity;
use App\Models\News;
use App\Models\Page;
use App\Models\Review;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserExtra;

class HomeController extends Controller
{
    use \Artesaos\SEOTools\Traits\SEOTools;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if (auth()->check()) {
            $oUser = auth()->user();

            if ($oUser->role_id === User::ROLE_ADMIN) {
                //return view('admin.home');
                return redirect()->to('/backend');
            } elseif ($oUser->role_id === User::ROLE_TEAM) {
                if (!$oUser->terms_date) {
                    return view('teams.invite', ['hideNav' => true]);
                }

                return view('teams.choice', []);
            } else {
                $this->seo()
                    ->setCanonical(config('app.url'))
                    ->setTitle(trans('main.home_title'))
                    ->setDescription(trans('main.home_description'));

                $news = News::take(3)->latest()->get();

                if ($oUser->isInTrial()) {
                    $dateEnd = new \DateTime($oUser->date_trialend);
                    $o['dateEnd'] = $dateEnd->format('d.m.Y');
                    $dateDeactivate = new \DateTime($oUser->date_trialend);
                    $dateDeactivate->add(new \DateInterval('P30D')); // 30 days
                    $o['dateDeactivate'] = $dateDeactivate->format('d.m.Y');

                    $dateNow = new \DateTime();
                    $dateInterval = $dateEnd->diff($dateNow);

                    $days = $dateInterval->days;
                    $day = trans_choice('main.days', $days, ['days' => $days]);

                    $hours = $dateInterval->format('%h');
                    $hour = trans_choice('main.hours', $hours, ['hours' => $hours]);

                    $o['dateInterval'] = "$day"; //  $hour

                    if ($dateNow > $dateEnd) {
                        $o['trialEnded'] = true;
                    }
                } else {
                    $order = UserAccounting::where('usr_id', '=', $oUser->id)->where('activity_type', '=', Activity::PACKAGE)->orderBy('accounting_id', 'DESC')->first();
                    $o['aExtraBookings'] = UserExtra::where('usr_id', '=', $oUser->id)->whereDate('date_end', '>', date('Y-m-d H:i:s'))->get();

                    if ($order) {
                        $o['packagePaidDate'] = $order->date_end;
/*                        $period = new \DatePeriod(new \DateTime($order->date_start), \DateInterval::createFromDateString('1 month'),
                            new \DateTime($order->date_end));

                        $dateNow = new \DateTime();
                        // TODO: BUG: This does not respect the time (hour/minutes) which leads to a wrong time below (BUG: Part 2)
                        $dtNext = (new \DateTime($order->date_end))->modify('+5 minutes');

                        foreach ($period as $dt) {
                            if ($dt > $dateNow) {
                                $dtNext = $dt;
                                break;
                            }
                        }
                        // BUG: Workaround
                        $dtNext->modify('+1 day');
                        $o['renewTime'] = "{$dtNext->format("d")}.{$dtNext->format("m")}.{$dtNext->format("Y")}";*/
                        // BUG: Part 2
                        //$o['renewTime'] = "{$dtNext->format("d")}.{$dtNext->format("m")}.{$dtNext->format("Y")}, {$dtNext->format("H")}:{$dtNext->format("i")} Uhr";

                        $accountingTimes = get_user_accounting_times(auth()->id());
                        $o['renewTime'] = $accountingTimes['nextTimeFormatted'];
                    }

                    $o['isBlocked'] = $oUser->is_blocked;
                }

                //$page = Page::where('title', '=', 'dashboard')->first();
                //$media = $page->getFirstMedia('bg');

                $offers = [
                    'steady' => [
                        'title' => 'Monetarisiere Deinen Podcast mit Steady - Zahle 3 Monate nichts',
                        'body' => 'Steady ist ein Startup aus Berlin. Über Steady können Dich Deine Hörer mit kostenpflichtigen Mitgliedschaften unterstützen. Steady hat bereits über 4 Millionen &euro; für seine Produzenten eingesammelt. <br><br><a href="https://pdcst.de/steady" target="_blank">Zum Steady-Angebot</a>',
                    ],
/*                    'hindenburg' => [
                        'title' => '90 Tage Probephase - statt 30 Tage - für die Software Hindenburg PRO',
                        'body' => 'Wenn Du Dich auf das Geschichten erzählen und nicht den Audioschnitt konzentrieren möchtest, dann <a href="https://hindenburg.com/products/hindenburg-journalist-pro" target="_blank">schau Dir Hindenburg PRO</a> an. <br><br><a href="https://pdcst.de/90hindenburg" target="_blank">Zum Download-Formular für die 90-tägige Test-Version</a>',
                    ],*/
                ];

                $hideReviewButton = Review::owner()->first() || $oUser->isInTrial();

                return view('home', compact('news', 'o' /*, 'page', 'media'*/, 'offers', 'hideReviewButton'));
            }
        } else {
            $this->seo()
                ->setCanonical(config('app.url'))
                ->setTitle(trans('main.page_title'))
                ->setDescription(trans('main.page_description'));

            $reviews = Cache::get(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, []);
            shuffle($reviews);

            $page = Page::where('title', '=', 'home')->first();

            return view('welcome', ['reviews' => $reviews, 'page' => $page]);
        }
    }
}
