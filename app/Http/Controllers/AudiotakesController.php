<?php

namespace App\Http\Controllers;

use App\Classes\Adswizz\DomainApiManager;
use App\Classes\Adswizz\NoPublisherFoundException;
use App\Classes\Adswizz\QueryBuilder;
use App\Classes\AudiotakesManager;
use App\Events\FeedUpdateEvent;
use App\Http\Requests\AudiotakesContractRequest;
use App\Models\AudiotakesBankTransfer;
use App\Models\AudiotakesContract;
use App\Models\AudiotakesContractPartner;
use App\Models\AudiotakesPayout;
use App\Models\Feed;
use App\Models\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AudiotakesController extends Controller
{
    use SEOTools;

    public function index()
    {
        $this->seo()
            ->setTitle(trans('audiotakes.page_title_index'));

        $contracts = AudiotakesContract::owner()->withTrashed()->get();
        $contractPartners = $this->getContractPartnerList();
        $feeds = Feed::owner()->get();
        //$payoutFundsSum = round(AudiotakesPayout::owner()->sum('funds_open'), PHP_ROUND_HALF_DOWN);
        $payoutFundsSum = AudiotakesManager::getFunds(auth()->id());
        $countryList = \Countrylist::getList('de');

        // Used to display the Amazon ToC
        return view('audiotakes.index', compact('contracts', 'contractPartners', 'feeds', 'payoutFundsSum', 'countryList'));
    }

    public function show()
    {
        $this->seo()
            ->setTitle(trans('audiotakes.page_title_terms'));

        $validated = \request()->validate([
            'first_name' => 'nullable|string|min:2',
            'last_name' => 'nullable|string|min:2',
            'email' => 'nullable|string|min:6',
            'street' => ['nullable', 'string', 'max:50'],
            'housenumber' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:50'],
            'post_code' => ['nullable', 'max:10'],
            'country' =>  [
                'nullable',
                //Rule::in(array_keys($countries)),
            ],
            'organisation' => ['nullable', 'string', 'max:255'],
            'feed_title' => ['nullable', 'string', 'max:255'],
            'feed_id' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['hideNav'] = true;
        $feed = Feed::owner()->where('feed_id', '=', $validated['feed_id'])->firstOrFail();
        $validated['feed_url'] = get_feed_uri($feed->feed_id, $feed->domain);
        $validated['share'] = AudiotakesContract::DEFAULT_SHARE;

        return view('audiotakes.show', $validated);
    }

    public function create()
    {
        $validated = $this->validate(request(), [
            'feed_id' => ['required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
        ], [], [
            'feed_id' => 'ID des Podcasts', // I18N
        ]);
        $feedId = Arr::pull($validated, 'feed_id');
        $feed = Feed::owner()->findOrFail($feedId);
        $contracts = AudiotakesContract::owner()->get();
        $contains = $contracts->contains(function ($value) use ($feedId) {
            return $value->feed_id == $feedId;
        });

        if ($contains) {
            return response()->redirectToRoute('audiotakes.index')->with(['status' => trans('feeds.warning_contract_exists')]);
        }

        $this->seo()
            ->setTitle(trans('audiotakes.page_title_add_contract', ['title' => $feed->rss['title']]));

        $userdata = collect(auth()->user()->only(['first_name', 'last_name', 'street', 'housenumber', 'post_code', 'city', 'country', 'telephone', 'email', 'organisation', 'vat_id']));
        $contractPartners = $this->getContractPartnerList();
/*        $countries = collect(Countries::select(['iso_3166_2', 'name'])->get())->map(function($v) {
            return [
                'value' => $v->iso_3166_2,
                'text' => $v->name,
            ];
        });*/

        $locale = Str::before(app()->getLocale(), '-');
        $countries = [];
        foreach(\CountryState::getCountries($locale) as $key => $value) {
            $countries[] = [
                'value' => $key,
                'text' => $value,
            ];
        }

        return view('audiotakes.create', compact('userdata', 'feed', 'contracts', 'contractPartners', 'countries'));
    }

    /**
     * @param  AudiotakesContractRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AudiotakesContractRequest $request)
    {
        $msg = ['success' => trans('audiotakes.success_contract_for_feed_created')];

        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $userId = auth()->id();
            $username = auth()->user()->username;
            $feedId = $validated['feed_id'];
            $user = Arr::pull($validated, 'user');
            $identifier = AudiotakesContract::getFreeIdentifier();
            $user['feed_id'] = $feedId;
            $user['user_id'] = $userId;
            $user['audiotakes_contract_partner_id'] = $validated['audiotakes_contract_partner_id'];
            $user['audiotakes_date_accepted'] = now();
            $user['created_at'] = now();
            $user['share'] = AudiotakesContract::DEFAULT_SHARE;

            if (!$identifier) {
                throw new \Exception(trans('audiotakes.error_message_getting_identifier_failed'));
            }

            $ac = AudiotakesContract::whereIdentifier($identifier)->firstOrFail();
            $res = $ac->update($user);

            if (!$res) {
                throw new \Exception(trans('audiotakes.error_message_saving_identifier_failed'));
            }

            $feed = Feed::owner()->where('feed_id', '=', $feedId)->firstOrFail();
            $settings = $feed->settings;
            $settings['audiotakes_id'] = $identifier;
            $settings['audiotakes'] = 1;
            $settings['chartable'] = $settings['rms'] = $settings['podcorn'] = $settings['podtrac'] = 0;

            if (!$feed->whereFeedId($feedId)->whereUsername($username)->update(['settings' => $settings])) {
                throw new \Exception(trans('audiotakes.error_message_setting_identifier_failed'));
            }

            $entries = $feed->entries;

            foreach($entries as &$entry) {
                try {
                    if (isset($entry->show_media) && !empty($entry->show_media)) {
                        $file = get_file($username, $entry->show_media);
                        if ($file) {
                            $entry['audiotakes_guid'] = sha1($file['name']);
                        }
                    }
                } catch (\Exception $e) {
                }
            }
            $feed->whereUsername($username)
                ->whereFeedId($feedId)
                ->update([
                    'entries' => array_values($entries)
                ]);

            //auth()->user()->update(['audiotakes_enabled' => true]);
            // Update feed to reflect changes in download urls
            event(new FeedUpdateEvent($feed->username, $feed->feed_id));
        }, 3);

        return response()->json($msg);
    }

    public function ids()
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }

        foreach (AudiotakesContract::all() as $id) {
            echo $id->identifier . PHP_EOL;
        }
    }

    public function stats($id)
    {
        $contract = AudiotakesContract::owner()->whereIdentifier($id)->firstOrFail();
        $feed = Feed::owner()->whereFeedId($contract->feed_id)->firstOrFail();
        $feedTitle = $feed->rss['title'];
        $this->seo()
            ->setTitle(trans('audiotakes.page_title_stats', ['feed' => $feedTitle]));

        $from = now()->subMonth()->toAtomString();
        $to = today()->toAtomString();
        $now = now()->toAtomString();

        try {
            $ada = new DomainApiManager();
            $publisherId = $ada->getPublisherId($id);

            $qb = new QueryBuilder();
            $qb->setInterval(['from' => $from, "to" => $to]);
            $qb->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum", "fillRate"]);
            $qb->setPublisher($publisherId);
            $qb->addSplitter(["id" => "__time", "granularity" => "DAY", "limit" => 25]);
            //$qb->setSort('inventory', 'DESC');
            $results = $ada->queryAnalytics($qb->get(), 'AUDIOMAX');

            $age = new QueryBuilder();
            $age->setInterval(['from' => $from, "to" => $to]);
            $age->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum"]);
            $age->setPublisher($publisherId);
            $age->addSplitter(["id" => "requestVariables_aw_0_awz.a_name", "limit" => 25]);
            //$qb->setSort('objectiveCountableSum', 'DESC');
            $_ages = $ada->queryAnalytics($age->get(), 'AUDIOMAX');
            $ci = 0;
            $ages = [];
            foreach($_ages->data as $entry) {
                $a = $entry->total->{'requestVariables_aw_0_awz.a_name'} ?? 'undefined';
                if ($a !== 'undefined') {
                    $a = Str::before($a, '(');
                } else {
                    $a = trans('audiotakes.label_other');
                }
                $i = $entry->total->inventory;
                $ages[$a] = ['inventory' => $i];
                $ci += $i;
            }
            foreach($ages as &$entry) {
                $entry['percent'] = round($entry['inventory'] / $ci * 100, 2);
            }

            $ranges = [
                'today' => ['from' => $to, 'to' => now()->toAtomString()],
                'yesterday' => ['from' => now()->subDay()->toAtomString(), 'to' => $to],
                'last7days' => ['from' => now()->subDays(7)->toAtomString(), 'to' => $to],
                'last30days' => ['from' => now()->subDays(30)->toAtomString(), 'to' => $to],
                'thisMonth' => ['from' => now()->startOfMonth()->toAtomString(), 'to' => $now]
            ];

            foreach ($ranges as $key => $range) {
                $revenue = new QueryBuilder();
                $revenue->setInterval(['from' => $range['from'], "to" => $range['to']]);
                $revenue->setMetrics(["supplyRevenueInUSD"]);
                $revenue->setPublisher($publisherId);
                $revenue = $ada->queryAnalytics($revenue->get(), 'AUDIOMAX');
                $aRevenue[$key] = AudiotakesManager::calculateFunds($revenue->total->supplyRevenueInUSD, $contract->share);
            }

            $reg = new QueryBuilder();
            $reg->setInterval(['from' => $from, "to" => $to]);
            $reg->setMetrics(["inventory", "objectiveCountableSum"]);
            $reg->setPublisher($publisherId);
            $reg->addSplitter(["id" => "geoCountryName", "limit" => 5]);
            $reg->addSplitter(["id" => "geoRegionName", "limit" => 10]);
            $reg->addSplitter(["id" => "geoCity", "limit" => 15]);
            $reg->setSort('objectiveCountableSum', 'DESC');
            $regions = $ada->queryAnalytics($reg->get(), 'AUDIOMAX');
        } catch (NoPublisherFoundException $e) {
            return response()->redirectTo('/audiotakes')->with([
                'error' => trans('audiotakes.message_failure_retrieving_stats_for_publisher', ['id' => $id])
            ]);
        } catch (\Exception $e) {
            return response()->redirectTo('/audiotakes')->with([
                'error' => $e->getMessage()
            ]);
        }

        $from = now()->subMonth()->format('d.m.Y'); // TODO: I18N
        $to = now()->format('d.m.Y'); // TODO: I18N

        return view('audiotakes.stats', compact('results', 'ages', 'aRevenue', 'regions', 'from', 'to', 'feedTitle'));
    }

    public function adoptin()
    {
        if (!in_array(auth()->id(), [13708, 1])) {
            abort(404);
        }

        $optins = Feed::where('settings.ads', '=', '1')
            ->select(['username', 'feed_id', 'rss.title', 'domain'])
            ->get();
        $usernames = $optins
            ->pluck('username')
            ->toArray();
        $leads = User::whereIn('username', $usernames)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['username'] => ['user' => $item]];
            })->toArray();

        foreach($optins as $optin) {
            if (array_key_exists($optin->username, $leads)) {
                $leads[$optin->username]['feed'] = $optin;
                $leads[$optin->username]['url'] = '';

                if (isset($optin->domain['feed_redirect']) && $optin->domain['feed_redirect']) {
                    $leads[$optin->username]['redirect'] = $optin->domain['feed_redirect'];
                }
            }
        }

        return view('audiotakes.adoptin', compact('leads'));
    }

    public function creditvoucher($id)
    {
        if (in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])) {
            $abt = AudiotakesBankTransfer::findOrFail($id);
            $username = $abt->user->username;
        } else {
            $abt = AudiotakesBankTransfer::owner()->findOrFail($id);
            $username = auth()->user()->username;
        }

        $file = storage_path(AudiotakesBankTransfer::CN_STORAGE_DIR . get_user_path($username)) . DIRECTORY_SEPARATOR . $abt->billing_number . AudiotakesBankTransfer::CN_EXTENSION;

        if (!File::exists($file)) {
            $file = $abt->saveBill();
        }
        $filename = basename($file);

        if ($file && File::exists($file) && File::isFile($file) && File::isReadable($file)) {
            return response()->download($file, $filename);
        }

        throw new \Exception(trans(('audiotakes.error_creditvoucher_could_not_be_created')));
    }

    private function getContractPartnerList()
    {
        return AudiotakesContractPartner::owner()->get()->map(function ($v) {
            $text = '';

            if ($v->organisation) {
                $text = $v->organisation . ', ';
            }

            $text .= $v->first_name . ' ' . $v->last_name . ', ' . $v->street . ' ' . $v->housenumber . ', ' . $v->post_code . ' ' . $v->city . ', ' . get_country_spelled($v->country);

            return [
                'value' => $v->id,
                'text' => $text
            ];
        });
    }
}
