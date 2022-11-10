<?php

namespace App\Console\Commands;

use App\Classes\UserAgent;
use DeviceDetector\DeviceDetector;
use Illuminate\Console\Command;

class EnhanceUserAgentDataCommand extends Command
{
    const INDEX_NAME = 'graylog_1';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:enhance-user-agent-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    private \ElasticAdapter\Documents\DocumentManager $documentManager;
    private DeviceDetector $deviceDetector;
    private \Elasticsearch\Client $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$ua = new UserAgent();
        //$data = $ua->getData("Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)");
        //$data = $ua->getData('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.524 Safari/537.36');

        $client = \Elasticsearch\ClientBuilder::fromConfig(config('elastic.client'));
        $this->documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
        $this->client = $client;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = 0;
        $limit = 100;
        $userAgent = new UserAgent();

        do {
            $agents = $this->getAgents($start, $limit, $total);

            foreach($agents as $aggregation) {
                foreach($aggregation->buckets() as $value) {
                    $ua = $value->key();
                    $this->line("Processing ua: " . $ua);
                    $data = $userAgent->getData($ua);
                    $this->updateEntries($ua, $data);
                }
            }
            $start = $start + $limit;
        } while($start < (100 || $total));

        return Command::SUCCESS;
    }

    private function getAgents($start, $limit, &$total)
    {
        $this->line("Fetching {$start} to " . ($start+$limit));

        $params =  [
            'bool' => [
                'must_not' => [
                    'exists' => [
                        'field' => 'user_agent_details'
                    ]
                ]
            ]
        ];
        $request = new \ElasticAdapter\Search\SearchRequest($params);
        $aggr = [
            'ua_count' => [
                'terms' => [
                    'field' => 'http_user_agent',
                    'size' => $limit,
                ]
            ]
        ];
        $request->aggregations($aggr);
        $response = $this->documentManager->search(self::INDEX_NAME, $request);
        $total = $response->total();

        return $response->aggregations();
    }

    private function updateEntries($ua, $data)
    {
        $params = [
            'index' => self::INDEX_NAME,
            'wait_for_completion' => false,
            'slices' => 'auto',
            'body' => [
                'query' => [
                    'term' => [
                        'http_user_agent' => $ua
                    ]
                ],
                'script' => [
                    'lang' => 'painless',
                    'inline' => 'ctx._source.user_agent_details = params.data',
                    'params' => [
                        'data' => $data
                    ]
                ]
            ],
        ];
        $this->client->updateByQuery($params);
    }
}
