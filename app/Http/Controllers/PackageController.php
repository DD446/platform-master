<?php

namespace App\Http\Controllers;

use Artesaos\SEOTools\Traits\SEOTools;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;
use App\Classes\UserAccountingManager;
use App\Http\Requests\PackageChangeRequest;
use App\Models\Faq;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\PackageFeature;
use App\Models\Review;
use App\Models\User;
use App\Models\UserQueue;
use App\Scopes\IsVisibleScope;
use Stevebauman\Location\Facades\Location;

class PackageController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $this->seo()
            ->setTitle(trans('package.page_title_packages'))
            ->setDescription(trans('package.page_description_packages'));

        if (\auth()->check()) {
            // We do not display these for logged-in users
            $reviews = $faqs = null;
            //return redirect()->to('/pakete/verwaltung/');
        } else {
            $reviews = Cache::get(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, []);
            $reviews = array_slice($reviews, 0, 4);
            shuffle($reviews);

            $faqs = Faq::whereIn('faq_id', [1, 2, 4, 9, 14, 36])->take(6)->get();
        }
        $aPackages = Package::withoutGlobalScope(IsVisibleScope::class)
            ->where('monthly_cost', '>', 0)
            ->where('is_hidden', '=', 0)
            ->get();
        $aFeatures = $this->getFeatureList(Package::CACHE_KEY_FEATURE_LIST_ALL);

        return view('package.index', compact('aPackages', 'aFeatures', 'reviews', 'faqs'));
    }

    /**
     * @param  string  $type
     * @return mixed
     */
    private function getFeatureList($type = Package::CACHE_KEY_FEATURE_LIST)
    {
        $aFeatureList = Cache::get($type, []);

        if (count($aFeatureList) > 0) {
            return $aFeatureList;
        }

        if ($type == Package::CACHE_KEY_FEATURE_LIST_ALL) {
            $aPackages = Package::with(['mappings'])
                ->withoutGlobalScope(IsVisibleScope::class)
                ->where('monthly_cost', '>', 0)
                ->where('is_hidden', '=', 0)
                ->get();
        } else {
            $aPackages = Package::with(['mappings'])->get();
        }
        $aFeatures = PackageFeature::all();
        $aAllowedFeatures = [
            'shows',
            'feeds',
            //'feeds_extra',
            'storage',
            //'storage_extra',
            'statistics',
            'blogs',
            //'api',
            //'bill_online',
            //'bill_print',
            'domains',
            'subdomains',
            'subdomains_premium',
            //'transcoding',
            'protection',
            //'protection_user', // TODO
            'scheduler',
            'auphonic',
            'ads',
            'spotify',
            'ssl',
            'dsgvo',
            'podcastportals',
            //'bandwidth',
            //'multiuser',
            'support',
            'player',
            'player_configuration',
            'members',
            'downloads',
            'audiotakes',
        ];

        $aPackageFeatures = [];

        foreach($aPackages as $package) {
            foreach($package->mappings as $mapping) {
                $aPackageFeatures[$package->package_id][$mapping->feature->feature_name] = [
                    'units' => $mapping->units,
                    'status' => $mapping->status,
                ];
            }
        }

        $aFeatures->prepend(new PackageFeature(['feature_name' => 'shows']));
        $aFeatures->prepend(new PackageFeature(['feature_name' => 'downloads']));
        $aFeatures->add(new PackageFeature(['feature_name' => 'ssl']));
        $aFeatures->add(new PackageFeature(['feature_name' => 'dsgvo']));
        $aFeatures->add(new PackageFeature(['feature_name' => 'spotify']));
        $aFeatures->add(new PackageFeature(['feature_name' => 'podcastportals']));
        $aFeatures->add(new PackageFeature(['feature_name' => 'audiotakes']));

        foreach ($aFeatures as $feature) {
            if (!in_array($feature->feature_name, $aAllowedFeatures)) {
                continue;
            }

            // Fill list with features
            foreach ($aPackages as $package) {
                switch ($feature->feature_name) {
                    case 'shows':
                        $f = ['units' => trans('package.shows_unlimited'), 'status' => 1];
                        break;
                    case 'storage':
                        $byte = $aPackageFeatures[$package->package_id][$feature->feature_name]['units'];
                        $f = ['units' => get_size_readable($byte, 2, 1024) . ' (ca. ' . $this->getAudioTime($byte) .'h Audio)', 'status' => 1];
                        break;
                    case 'support':
                        if (isset($aPackageFeatures[$package->package_id][$feature->feature_name])) {
                            $units = $aPackageFeatures[$package->package_id][$feature->feature_name]['units'];
                        } else {
                            $units = 0;
                        }

                        if ($units > 0) {
                            $f = ['units' => trans('package.free_phone_support', ['units' => $units]), 'status' => 1];
                        } else {
                            $f = ['units' => trans('package.free_email_support'), 'status' => 1];
                        }
                        break;
                    case 'downloads':
                        $f = ['units' => trans('package.unlimited_downloads'), 'status' => 1];
                        break;
                    case 'podcastportals':
                    case 'spotify':
                    case 'dsgvo':
                    case 'ssl':
                    case 'audiotakes':
                        $f = ['units' => 0, 'status' => 1];
                        break;
                    default:
                        $f = ($aPackageFeatures[$package->package_id][$feature->feature_name] ?? ['units' => 0, 'status' => 0]);
                }
                $aFeatureList[$feature->feature_name][$package->package_name] = $f;
            }
        }

        Cache::forever($type, $aFeatureList);

        return $aFeatureList;
    }

    private function getBenefitList(): array
    {
        $aBenefits = Cache::get(Package::CACHE_KEY_BENEFITS, []);

        if (count($aBenefits) > 0) {
            return $aBenefits;
        }

        $aFeatureList = $this->getFeatureList(Package::CACHE_KEY_FEATURE_LIST_ALL);

        foreach($aFeatureList as $feature => $aFeature) {
            foreach($aFeature as $package => $a) {
                $aBenefits[$package][$feature] = $a;
            }
        }

        Cache::forever(Package::CACHE_KEY_BENEFITS, $aBenefits);

        return $aBenefits;
    }

    private function getBenefits(String $packageName): array
    {
        $aBenefits = $this->getBenefitList();

        if (!array_key_exists($packageName, $aBenefits)) {
            return [];
        }

        return $aBenefits[$packageName];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create($id)
    {
        $package = Package::withoutGlobalScope(IsVisibleScope::class)->findOrFail($id);
        $packageName = trans_choice('package.package_name', $package->package_name);

        $this->seo()
            ->setTitle(trans('package.page_title_show', ['name' => $packageName]));

        $reviews = Cache::get(Review::REVIEW_CACHE_KEY_LIST_WITH_LOGO, []);
        shuffle($reviews);
        $review = array_shift($reviews);

        $benefits = $this->getBenefits($package->package_name);

        if (!count($benefits)) {
            $benefits = $this->getBenefits($package->package_name);
        }

        // TODO: I18N
        $countries = \Countrylist::getList('de');

        // Exclude terratories
        foreach (['EZ', 'IO', 'TF', 'PS'] as $code) {
            unset($countries[$code]);
        }

        $countriesFirst = [
            'DE' => 'Deutschland',
            'AT' => 'Österreich',
            'CH' => 'Schweiz',
            'LI' => 'Liechtenstein',
        ];

        foreach ($countriesFirst as $code => $country) {
            unset($countries[$code]);
        }

        return view('package.create', compact('package', 'packageName', 'benefits', 'review', 'countries', 'countriesFirst'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $this->validate($request, [
            'id' => 'required|exists:package,package_id',
            'email' => 'required|email|unique:usr,email',
            //'country' => 'required|exists:countries,iso_3166_2',
            'terms' => 'required|in:yes',
        ], [], [
            'email' => trans('main.attribute_email'),
            'terms' => trans('main.attribute_terms'),
        ]);

        if ($request->has('country') || !$request->wantsJson()) {
            return redirect()->route('packages');
        }

        $msg = ['error' => trans('login.message_failure_preregistration')];

        $queuedUser = new UserQueue();
        $queuedUser->email = $request->get('email');
        $queuedUser->country = '--';
        $queuedUser->package_id = $id;
        $queuedUser->hash = Str::random(6);
        $queuedUser->username = User::getNewUsername();

        if ($request->has('source')) {
            $queuedUser->source = $request->get('source');
        }

        if ($queuedUser->save()) {
            //$msg = [/*'success' => trans('login.message_success_preregistration', ['email' => $queuedUser->email]), */'email' => $queuedUser->email];
            $msg = ['success' => trans('login.message_success_preregistration', ['email' => $queuedUser->email])];
        }

        if ($request->wantsJson()) {
            session()->put(['email' => $queuedUser->email, 'package_id' => $id]);
            return response()->json($msg);
        }

        return redirect()->route('package.show')->with($msg)->with(['package_id' => $id]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show()
    {
        $this->seo()->setTitle(trans('login.page_title_preregistration_hint'));

        if (!session('email')) {
            return redirect()->route('home');
        }

        $package = Package::withoutGlobalScope(IsVisibleScope::class)->findOrFail(session('package_id'));

        return view('package.show', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $username
     * @param $hash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($username, $hash)
    {
        $this->seo()->setTitle(trans('login.page_title_activation'));

        $userQueue = UserQueue::where('username', '=', $username)
            ->where('hash', '=', $hash)
            ->withTrashed()
            ->first();

        if (!$userQueue) {
            abort(404);
        }

        if ($userQueue->trashed()) {
            return redirect()->route('login')->with(['status' => trans('login.message_preregistration_finished')]);
        }

        $location = Location::get();
        $showCaptcha = !$location;
        //$showCaptcha = (!$location || $userQueue->country != $location->countryCode);
        $askCreditCard = ($userQueue->package_id > 6);

        // TODO: I18N
        $countries = \Countrylist::getList('de');

        // Exclude terratories
        foreach (['EZ', 'IO', 'TF', 'PS'] as $code) {
            unset($countries[$code]);
        }

        $countriesFirst = [
            'DE' => 'Deutschland',
            'AT' => 'Österreich',
            'CH' => 'Schweiz',
            'LI' => 'Liechtenstein',
        ];

        foreach ($countriesFirst as $code => $country) {
            unset($countries[$code]);
        }

        return view('package.edit', ['hideNav' => true, 'username' => $username, 'hash' => $hash, 'showCaptcha' => $showCaptcha, 'askCreditCard' => $askCreditCard, 'countries' => $countries, 'countriesFirst' => $countriesFirst]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $username, $hash)
    {
        $userQueue = UserQueue::where('username', '=', $username)
            ->where('hash', '=', $hash)
            ->firstOrFail();

        $rules = [
            'user' => 'required|exists:user_queue,username',
            'password' => 'required|min:6|different:user',
            'country' => 'required|exists:countries,iso_3166_2',
        ];

        $location = Location::get();

        // SPAM prevention
        if (!$location || $request->has('card_number')) {
            //$rules['mathcaptcha'] = 'required|mathcaptcha';
            //$rules['g-recaptcha-response'] = 'required|captcha';
            if ($userQueue->package_id > 6
                || in_array(Str::afterLast(Str::lower($userQueue->email), '@'), ['outlook.com', 'mail.ru', 'yahoo.com'])
                ) {
                $rules['card_number'] = ['required', new CardNumber];
                $rules['expiration_year'] = ['required', new CardExpirationYear($request->get('expiration_month'))];
                $rules['expiration_month'] = ['required', new CardExpirationMonth($request->get('expiration_year'))];
                $rules['cvc'] = ['required', new CardCvc($request->get('card_number'))];

                Log::debug("CC: " . $request->get('owner_name') . ", "  . $request->get('card_number'));
            }
        }

        $validated = $this->validate($request, $rules, [], [
            'password' => trans('login.validation_field_password'),
            'user' => trans('login.validation_field_username'),
            'country' => trans('login.validation_field_country')
        ]);

        $user = new User();
        $user->passwd = md5($validated['password']);
        $user->password = password_hash($validated['password'], PASSWORD_BCRYPT);
        $user->role_id = User::ROLE_USER;
        $user->email = $userQueue->email;
        $user->username = $userQueue->username;
        $user->package_id = $userQueue->package_id;
        $user->country = $validated['country'];
        $user->date_trialend = $userQueue->created_at->addDays(30);
        $user->date_created = $userQueue->created_at;
        $user->created_at = $userQueue->created_at;
        $user->updated_at = $user->last_updated = Carbon::now();
        $user->is_acct_active = true;
        $user->is_trial = User::IS_TRIAL;
        $user->terms_date = $userQueue->created_at;
        $user->privacy_date = $userQueue->created_at;
        $user->terms_version = User::TERMS_VERSION;
        $user->privacy_version = User::PRIVACYPOLICY_VERSION;
        $user->welcome_email_state = 1;

        if ($user->save()) {
            $userQueue->delete();
            Auth::guard('web')->login($user, true);

            return redirect()->route('podcast.wizard')->with(['success' => trans('login.message_success_registration')]);
        }

        return redirect()->back();
    }

    public function change(PackageChangeRequest $request)
    {
        $user = auth()->user();
        $uam = new UserAccountingManager();
        $msg = ['message' => trans('package.message_success_package_change')];

/*        if ($user->isInTrial()) {
            // Change user package
            $user->package_id = $request->id;
            $user->new_package_id = null;

            if (!$user->save()) {
                $msg = ['message' => trans('package.message_error_package_change')];
            }
        }
        // Save downgrade for processing on next billing period
        else*/if ($request->id < $user->package_id) {
            $newPackage = Package::findOrFail($request->id);
            $accountingTimes = get_user_accounting_times($user->id);
            $msg = ['message' => trans('package.message_success_package_downgrade_saved', ['name' => trans_choice('package.package_name', $newPackage->package_name), 'date' => $accountingTimes['nextTimeFormatted']])];
            $user->new_package_id = $request->id;

            if (!$user->save()) {
                $msg = ['message' => trans('package.message_error_package_change')];
            }
        } elseif (!$uam->changePackage(/** User */ $user, $request->id)) {
            //$msg = ['message' => trans('package.message_error_package_change')];
            throw new \Exception(trans('package.message_error_package_change'));
        }

        return response()->json($msg);
    }

    public function withdrawDowngrade()
    {
        $user = auth()->user();
        $user->new_package_id = null;

        $msg = ['success' => trans('package.message_success_downgrade')];

        if (!$user->save()) {
            $msg = ['error' => trans('package.message_error_downgrade_withdrawal_failed')];
        }

        return redirect()->route('packages')->with($msg);
    }

    public function delete()
    {
        \SEO::setTitle(trans('user.page_title_delete'));

        return view('package.delete');
    }

    private function getAudioTime($byte)
    {
        return round($byte/52428800, 0);
    }
}
