<?php

namespace App\Console\Commands;

use App\Classes\Adswizz\DomainApiManager;
use App\Classes\Adswizz\QueryBuilder;
use Illuminate\Console\Command;

/**
 * Class AdswizzDomainApi
 * http://docs.adswizz.com/domain-api/v5/
 * @package App\Console\Commands
 */
class AdswizzDomainApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adswizz:domain-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ada = new DomainApiManager();
        //$agency = $ada->getOurAgency();
        //$ada->getDeviceTypesAndManufacturers();
        //$publishers = $ada->getPublishers(500, 1);
        //$publisher = $ada->getPublisher(15);
        //$dmpProviders = $ada->getDmpProviders();
        //$audienceSegments = $ada->getAudienceSegments($dmpProviders[0]);
        //$dmpPublishers = $ada->getDmpPublishers();
/*        foreach ($publishers as $publisher) {
            $dmpPublisherValues = $ada->getDmpPublisherValues($publisher);
            if (count($dmpPublisherValues) > 0) {
                $d = $dmpPublisherValues;
            }
        }*/
/*        $dmpPodcastConsumptionGroups = $ada->getDmpPodcastConsumptionGroups();
        $dmpPodcastConsumptionValues = $ada->getDmpPodcastConsumptionValues($dmpPodcastConsumptionGroups[0]);*/
/*        $dmpPodcastContexualGroups = $ada->getDmpPodcastContexualGroups();
        $dmpPodcastContexualValues = $ada->getDmpPodcastContexualValues($dmpPodcastContexualGroups[7]);*/

        //$categories = $ada->getCategories();
        //$subcategories = $ada->getSubcategories();
        //$siteChannels = $ada->getSiteChannels();
        //$openrtbBuyers = $ada->getOpenrtbBuyers();
        //$metrics = $ada->getMetrics('AUDIOMAX');
        //$dimensions = $ada->getDimensions();

        // listenerIdHLL
/*        $query = $ada->queryAnalytics([
            "interval" => [
                "from" => "2021-05-28T15:22:39Z",
                "to" => "2021-05-28T15:22:39Z"
            ],
            "timezone" => "Europe/Berlin"
        ]);*/
/*        "metrics": [
        "supplyECPMInUSD",
        "objectiveCountableSum",
        "bidResponses",
        "bids",
        "listenerIdHLL"
    ], */

/*        "splitters": [
    {
        "id": "__time",
      "limit": 5,
      "granularity": "HOUR"
    } */

        // supplyPackSegmentName

        $q = json_decode('{
  "id": "supplyPackSegmentName",
  "filter": {
    "type": "AND",
    "fields": [
      {
        "id": "geoCity",
        "type": "IN",
        "values": [
          "berlin",
          "hamburg"
        ]
      },
      {
        "id": "deviceType",
        "type": "SEARCH",
        "query": "desktop"
      }
    ]
  },
  "interval": {
    "from": "2021-06-01T00:00:00Z",
    "to": "2021-08-04T23:59:59Z"
  },
  "limit": 20
}', true);
        //$res = $ada->getFilter($q);

# "objectiveCountableSum", // Impressions
# "listenerIdHLL", // Total Reach - Unique Users
# "inventory", // Inventory represents the sum opportunities, where an opportunity is the possibility to insert 1 or N ads (adbreak) to a stream/podcast/web page/… This metric is not available for sales channel, deal, deal id and advertiser dimensions
# "supplyInsertionRate", // Insertion Rate

$a = json_decode('{
  "interval": {
    "from": "2021-08-01T00:00:00Z",
    "to": "2021-08-05T23:59:59Z"
  },
  "metrics": [
    "objectiveCountableSum",
    "listenerIdHLL",
    "bidWons",
    "supplyInsertionRate",
    "fillRate",
    "supplyRevenueInUSD",
    "inventory"
  ],
  "filters": [
    {
      "id": "publisherName",
      "values": [
        "113"
      ],
      "exclusion": false
    }
  ],
  "splitters": [
    {
      "id": "__time",
      "granularity": "DAY",
      "limit": 25
    }
  ],
  "sort": {
    "id": "listenerIdHLL",
    "dir": "DESC"
  },
  "timezone": "Europe/Berlin"
}', true);

        $qb = new QueryBuilder();
        $qb->setInterval(['from' => "2021-08-01T00:00:00Z", "to" => "2021-08-05T23:59:59Z"]);
        $qb->setMetrics(["listenerIdHLL", "inventory", "objectiveCountableSum", "fillRate"]);
        $qb->setPublisher($ada->getPublisherId('at-owczl'));
        $qb->addSplitter(["id" => "__time", "granularity" => "DAY", "limit" => 25]);
        $qb->setSort('listenerIdHLL');
        $a = $qb->get();

        //$result = $ada->queryAnalytics($a, 'AUDIOMAX');

        //$supplyPacks = $ada->getSupplyPacks();
        //$supplyPack = $ada->getSupplyPack(3);

        return 0;
    }
}
