<?php

namespace App\Http\Controllers\API;

use App\Classes\Adswizz\DomainApiManager;
use App\Classes\Adswizz\QueryBuilder;
use App\Classes\AudiotakesManager;
use App\Http\Controllers\Controller;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class StatsPioniereController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function earnings()
    {
        $validated = $this->validate(\request(), [
            'i' => ['nullable', 'string', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
        ]);

        // audiotakes/Adswizz ID
        $ada = new DomainApiManager();
        $to = today()->startOfDay()->toAtomString();
        $now = now()->toAtomString();
        $ranges = [
            'today' => ['from' => $to, 'to' => now()->toAtomString()],
            'yesterday' => ['from' => now()->subDay()->startOfDay()->toAtomString(), 'to' => $to],
            'last7days' => ['from' => now()->subDays(7)->startOfDay()->toAtomString(), 'to' => $to],
            'last30days' => ['from' => now()->subDays(30)->startOfDay()->toAtomString(), 'to' => $to],
            'thisMonth' => ['from' => now()->startOfMonth()->startOfDay()->toAtomString(), 'to' => $now],
            //'funds' => ['from' => CarbonImmutable::createFromTimestamp(strtotime('2021-05-16 00:00:00'))->startOfDay()->toAtomString(), 'to' => $now],
        ];
        $share = [];

        if (\request()->filled('i')) {
            // audiotakes/Adswizz ID
            $identifier = $validated['i'];
            $publishers[$identifier] = $ada->getPublisherId($identifier);
            $share[$identifier] = AudiotakesContract::owner()->whereIdentifier($identifier)->value('share');
        } else {
            $contracts = AudiotakesContract::owner()->get();
            $publishers = $contracts->mapWithKeys(function($contract) use ($ada) {
                return [$contract->identifier => $ada->getPublisherId($contract->identifier)];
            })->toArray();
            $share = $contracts->mapWithKeys(function($contract) use ($ada) {
                return [$contract->identifier => $contract->share];
            })->toArray();
        }

        if (count($publishers) < 1) {
            throw new \Exception("No publisher found");
        }

        foreach ($ranges as $key => $range) {
            $sum = 0;
            foreach ($publishers as $identifier => $publisherId) {
                $revenue = new QueryBuilder();
                $revenue->setInterval(['from' => $range['from'], "to" => $range['to']]);
                $revenue->setMetrics(["supplyRevenueInUSD"]);
                $revenue->setPublisher($publisherId);
                $_revenue = $ada->queryAnalytics($revenue->get(), 'AUDIOMAX');
                $sum += AudiotakesManager::calculateFunds($_revenue->total->supplyRevenueInUSD, $share[$identifier]);
            }
            $aRevenue[$key] = round($sum, 2, PHP_ROUND_HALF_DOWN);
        }

        //$aRevenue['funds'] = round(AudiotakesPayout::owner()->sum('funds_open'), 2, PHP_ROUND_HALF_DOWN);
        $aRevenue['funds'] = AudiotakesManager::getFunds(auth()->id());

        return response()->json($aRevenue);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function summary()
    {
        $validated = $this->validate(\request(), [
            //'page' => 'array:number,size',
            //'page.number' => 'nullable|integer|gte:1',
            //'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            'i' => ['nullable', 'string', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
        ]);
        $from = CarbonImmutable::createFromTimestamp(strtotime($validated['df']))->startOfDay()->toAtomString();
        $to = CarbonImmutable::createFromTimestamp(strtotime($validated['dt']))->endOfDay()->toAtomString();

        // audiotakes/Adswizz ID
        $ada = new DomainApiManager();

        if (\request()->filled('i')) {
            // audiotakes/Adswizz ID
            $publishers[] = $ada->getPublisherId($validated['i']);
        } else {
            $publishers = AudiotakesContract::owner()->get()->map(function($contract) use ($ada) {
                return $ada->getPublisherId($contract->identifier);
            })->toArray();
        }

        if (count($publishers) < 1) {
            throw new \Exception("No publisher found");
        }

        $qb = new QueryBuilder();
        $qb->setInterval(['from' => $from, "to" => $to]);
        $qb->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum", "fillRate"]);
        $qb->setPublisher($publishers);
        $qb->addSplitter(["id" => "__time", "granularity" => "DAY", "limit" => 25]);
        //$qb->setSort('inventory', 'DESC');
        $results = $ada->queryAnalytics($qb->get(), 'AUDIOMAX');
        $res = $results->total;
        $res->fillRate = round($results->total->fillRate, 2);

        return response()->json($res);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function hits()
    {
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            'i' => ['nullable', 'string', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
        ]);
        $df = CarbonImmutable::createFromTimestamp(strtotime($validated['df']))->startOfDay();
        $from = $df->toAtomString();
        $dt = CarbonImmutable::createFromTimestamp(strtotime($validated['dt']))->endOfDay();
        $to = $dt->toAtomString();
        $limit = $df->diffInDays($dt);

        $ada = new DomainApiManager();
        // audiotakes/Adswizz ID
        if (\request()->filled('i')) {
            // audiotakes/Adswizz ID
            $publishers[] = $ada->getPublisherId($validated['i']);
        } else {
            $publishers = AudiotakesContract::owner()->get()->map(function($contract) use ($ada) {
                return $ada->getPublisherId($contract->identifier);
            })->toArray();
        }

        if (count($publishers) < 1) {
            throw new \Exception("No publisher found");
        }

        $qb = new QueryBuilder();
        $qb->setInterval(['from' => $from, "to" => $to]);
        $qb->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum", "fillRate"]);
        $qb->setPublisher($publishers);
        $qb->addSplitter(["id" => "__time", "granularity" => "DAY", "limit" => $limit /*$validated['page']['size'], "page" => $validated['page']['number']*/]);
        //$qb->setLimit($validated['page']['size']);
        $res = $ada->queryAnalytics($qb->get(), 'AUDIOMAX');
        $results = [];

        foreach($res->data as $o) {
            $a = [];
            foreach ($o->total as $key => $i) {
                if ($key == 'fillRate') {
                    $i = round($i, 2) . '%';
                } elseif ($key == '__time') {
                    $i = CarbonImmutable::createFromTimestamp(strtotime($i->start))->isoFormat('DD.MM.YYYY'); // TODO: I18N
                }

                $a[trans('audiotakes.adswizz_metrics.' . $key)] = $i;
            }

            $results[] = $a;
        }

        return response()->json(['items' => $results, 'count' => $limit]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function region()
    {
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            'i' => ['nullable', 'string', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
        ]);

        $df = CarbonImmutable::createFromTimestamp(strtotime($validated['df']))->startOfDay();
        $from = $df->toAtomString();
        $dt = CarbonImmutable::createFromTimestamp(strtotime($validated['dt']))->endOfDay();
        $to = $dt->toAtomString();
        //$limit = $df->diffInDays($dt);
        $limit = 15;
        $ada = new DomainApiManager();

        if (\request()->filled('i')) {
            // audiotakes/Adswizz ID
            $publishers[] = $ada->getPublisherId($validated['i']);
        } else {
            $publishers = AudiotakesContract::owner()->get()->map(function($contract) use ($ada) {
                return $ada->getPublisherId($contract->identifier);
            })->toArray();
        }

        if (count($publishers) < 1) {
            throw new \Exception("No publisher found");
        }

        $qb = new QueryBuilder();
        $qb->setInterval(['from' => $from, "to" => $to]);
        $qb->setMetrics(["inventory", "objectiveCountableSum"]);
        $qb->setPublisher($publishers);
        $qb->addSplitter(["id" => "geoCountryName", "limit" => $limit]);
        $qb->addSplitter(["id" => "geoRegionName", "limit" => $limit]);
        $qb->addSplitter(["id" => "geoCity", "limit" => $limit]);
        $qb->setSort('objectiveCountableSum', 'DESC');
        $res = $ada->queryAnalytics($qb->get(), 'AUDIOMAX');
        $results = [];

        foreach($res->data as $o) {
            $a = [];
            foreach ($o->total as $key => $i) {
                if ($key == '__time') {
                    $i = CarbonImmutable::createFromTimestamp(strtotime($i->start))->isoFormat('DD.MM.YYYY'); // TODO: I18N
                }
                $a[trans('audiotakes.adswizz_metrics.' . $key)] = Str::before($i, '(');
            }
            $a['regional_data'] = $o->data;
            $results[] = $a;
        }

        return response()->json($results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @hideFromAPIDocumentation
     */
    public function demographic()
    {
        $validated = $this->validate(\request(), [
            'page' => 'array:number,size',
            'page.number' => 'nullable|integer|gte:1',
            'page.size' => 'nullable|integer|gte:1',
            'df' => 'date|required',
            'dt' => 'date|required',
            'i' => ['nullable', 'string', 'exists:App\Models\AudiotakesContract,identifier,user_id,' . auth()->id()],
        ]);
        $from = CarbonImmutable::createFromTimestamp(strtotime($validated['df']))->startOfDay()->toAtomString();
        $to = CarbonImmutable::createFromTimestamp(strtotime($validated['dt']))->endOfDay()->toAtomString();

        $ada = new DomainApiManager();
        // audiotakes/Adswizz ID

        if (\request()->filled('i')) {
            // audiotakes/Adswizz ID
            $publishers[] = $ada->getPublisherId($validated['i']);
        } else {
            $publishers = AudiotakesContract::owner()->get()->map(function($contract) use ($ada) {
                return $ada->getPublisherId($contract->identifier);
            })->toArray();
        }

        if (count($publishers) < 1) {
            throw new \Exception("No publisher found");
        }

        $qb = new QueryBuilder();
        $qb->setInterval(['from' => $from, "to" => $to]);
        $qb->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum"]);
        $qb->setPublisher($publishers);
        $qb->addSplitter(["id" => "requestVariables_aw_0_awz.a_name", "limit" => 25]);
        //$qb->setSort('objectiveCountableSum', 'DESC');
        $_ages = $ada->queryAnalytics($qb->get(), 'AUDIOMAX');
        $ci = 0;
        $results = [];
        foreach($_ages->data as $entry) {
            $a = $entry->total->{'requestVariables_aw_0_awz.a_name'} ?? 'undefined';
            if ($a !== 'undefined') {
                $a = Str::before($a, '(');
            } else {
                $a = trans('audiotakes.label_other');
            }
            $i = $entry->total->inventory;
            $results[] = [trans('audiotakes.adswizz_metrics.age_group') => $a, trans('audiotakes.adswizz_metrics.inventory') => $i];
            $ci += $i;
        }
        foreach($results as &$entry) {
            $entry[trans('audiotakes.adswizz_metrics.spread')] = round($entry[trans('audiotakes.adswizz_metrics.inventory')] / $ci * 100, 2) . '%';
        }

        return response()->json($results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function contracts()
    {
        $contracts = AudiotakesContract::owner()->select(['feed_id', 'identifier'])->get();
        $aContracts = $contracts->mapWithKeys(function($contract) { return [$contract->feed_id => $contract->identifier]; })->toArray();
        $feeds = Feed::owner()->select(['feed_id', 'rss.title'])->whereIn('feed_id', array_keys($aContracts))->get();

        $res = $feeds->mapWithKeys(function($feed) use ($aContracts) {
            return [$feed->feed_id => [
                'identifier' => $aContracts[$feed->feed_id],
                'title' => $feed->rss['title'],
                'feed_id' => $feed->feed_id,
            ]];
        });

        return response()->json($res);
    }
}
