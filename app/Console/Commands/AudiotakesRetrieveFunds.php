<?php

namespace App\Console\Commands;

use App\Classes\Adswizz\DomainApiManager;
use App\Classes\Adswizz\QueryBuilder;
use App\Classes\AudiotakesManager;
use App\Models\AudiotakesContract;
use App\Models\AudiotakesPayout;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class AudiotakesRetrieveFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:retrieve-funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls earnings from Audiomax server into database on monthly base';

    private DomainApiManager $ada;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->ada = new DomainApiManager();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $holdback = AudiotakesContract::PLATFORM_HOLDBACK;
        $preshare = AudiotakesContract::GENERAL_SUBSTRACTION;
        $currency = 'EUR';
        for ($i = 1; $i > 0; $i--) {
            $date = CarbonImmutable::now()->subMonths($i);

            foreach (AudiotakesContract::cursor() as $contract) {
                $id = $contract->identifier;
                $this->line("Processing contract with ID {$id}.");
                if ($contract && $contract->user && $contract->user->withTrashed()) {
                    try {
                        $publisherId = $this->ada->getPublisherId($id);
                        $rawRevenue = $this->getRevenue($date, $publisherId);

                        if ($rawRevenue > 0) {
                            $funds = AudiotakesManager::calculateFunds($rawRevenue, $contract->share);
                            $ap = AudiotakesPayout::updateOrCreate(
                                [
                                    'user_id' => $contract->user_id,
                                    'audiotakes_contract_id' => $contract->id,
                                    'month' => $this->dateToMonth($date),
                                    'year' => $this->dateToYear($date)
                                ],
                                [
                                    'funds' => $funds,
                                    'funds_open' => $funds,
                                    'funds_raw' => $rawRevenue,
                                    'holdback' => $holdback,
                                    'share' => $contract->share,
                                    'preshare' => $preshare,
                                    'currency' => $currency,
                                ]
                            );
                            $this->line("User '{$contract->user->username}': ".$ap->wasRecentlyCreated ? ' Newly added' : ' Updated');
                        }
                    } catch (\Exception $e) {
                        $this->error("User '{$contract->user->username}': Did not find publisher id for contract '{$id}'.");
                    }
                } else {
                    $this->error("ERROR: Did not find user for contract '{$id}'.");
                }
            }
        }
        return 0;
    }

    /**
     * @param  array  $range
     * @param  string  $publisherId
     * @return mixed
     * @throws \Exception
     */
    private function getRevenue(CarbonImmutable $date, string $publisherId)
    {
        $revenue = new QueryBuilder();
        $revenue->setInterval(['from' => $date->startOfMonth()->toAtomString(), "to" => $date->endOfMonth()->toAtomString()]);
        $revenue->setMetrics(["supplyRevenueInUSD"]);
        $revenue->setPublisher([$publisherId]);
        $revenue = $this->ada->queryAnalytics($revenue->get(), 'AUDIOMAX');

        return $revenue->total->supplyRevenueInUSD;
    }

    /**
     * @param  CarbonImmutable  $date
     * @return string
     */
    private function dateToMonth(CarbonImmutable $date)
    {
        return $date->startOfMonth()->format('n');
    }

    /**
     * @param  CarbonImmutable  $date
     * @return string
     */
    private function dateToYear(CarbonImmutable $date)
    {
        return $date->startOfMonth()->format('Y');
    }
}
