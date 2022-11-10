<?php
/**
 * User: fabio
 * Date: 18.02.22
 * Time: 11:34
 */

namespace App\Classes;

use ElasticAdapter\Indices\IndexBlueprint;
use ElasticAdapter\Indices\IndexManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Support\Query;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ElasticManager
{
    const INDEX = 'graylog_*';

    public function search()
    {
        $client = \Elasticsearch\ClientBuilder::fromConfig(config('elastic.client'));
        $documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
        $indexName = 'graylog_*';
        $indexName = 'user_agent_index';

$params =[
    'bool' => [
        'must' => [
            [
                'term' => [
                    //'domain' => 'podcast-plattform.podcaster.de',
                    'domain' => 'oe1owy.podcaster.de',
                ]
            ],
            [
                'term' => [
                    //'feed' => 'podcastde-news',
                    'feed' => 'ETDPODCAST',
                ]
            ],
            [
                'term' => [
                    'request_method' => 'GET',
                ]
            ],
/*            [
                'term' => [
                    'media' => '220121_NAPS_DerZahnputzcast_mixdown-out.mp3',
                ]
            ],*/
            [
                'range' => [
                    'status' => [
                        'gte' => 200,
                        'lte' => 206
                    ],
                ]
            ],
            [
                'range' => [
                    'bytes_sent' => [ 'gt' => 0 ],
                ]
            ]
        ],
        'must_not' => [],
        'should' => [],
        'filter' => [],
    ]
];

$params0 =[
    'bool' => [
        'must' => [
            [
                'term' => [
                    'request_method' => 'GET',
                ]
            ],
            [
                'range' => [
                    'status' => [
                        'gte' => 200,
                        'lte' => 206
                    ],
                ]
            ],
            [
                'range' => [
                    'bytes_sent' => [ 'gt' => 0 ],
                ]
            ]
        ],
    ]
];

$params1 =[
    'bool' => [
        'must' => [
            [
                'term' => [
                    'domain' => 'podcast-plattform.podcaster.de',
                ]
            ],
            [
                'term' => [
                    'feed' => 'podcastde-news',
                ]
            ],
            [
                'term' => [
                    'request_method' => 'GET',
                ]
            ],
            [
                'range' => [
                    'status' => [
                        'gte' => 200,
                        'lte' => 201
                    ],
                ]
            ],
            [
                'range' => [
                    'bytes_sent' => [ 'gt' => 0 ],
                ]
            ]
        ],
    ]
];

$params2 = [
    'match' => [
        'domain' => 'podcast-plattform.podcaster.de',
    ]
];

$params3 =  [
    'match_all' => new \stdClass()
];

        // create a search request
        $request = new \ElasticAdapter\Search\SearchRequest($params3);

// configure highlighting
/*        $request->highlight([
            'fields' => [
                'message' => [
                    'type' => 'plain',
                    'fragment_size' => 15,
                    'number_of_fragments' => 3,
                    'fragmenter' => 'simple'
                ]
            ]
        ]);*/

// add suggestions
/*        $request->suggest([
            'message_suggest' => [
                'text' => 'test',
                'term' => [
                    'field' => 'message'
                ]
            ]
        ]);*/

// enable source filtering
        //$request->source(['media']);

// collapse fields
/*        $request->collapse([
            'field' => 'user'
        ]);*/

// aggregate data
        /*            'last_access' => [
                        'max' => [
                            'field' => 'time',
                        ],
                    ],*/

$aggr = [
    'count_media' => [
        'value_count' => [
            'field' => 'media'
        ]
    ]
];
$aggr = [
    'domain_count' => [
        'terms' => [
            'field' => 'domain',
            'size' => 10
        ]
    ],
    'media_count' => [
        'terms' => [
            'field' => 'media',
            'size' => 10
        ]
    ],
    'feed_count' => [
        'terms' => [
            'field' => 'feed',
            'size' => 10
        ]
    ],
    'referrer_count' => [
        'terms' => [
            'field' => 'http_referrer',
            'size' => 10
        ]
    ],
    'origin_count' => [
        'terms' => [
            'field' => 'origin',
            'size' => 10
        ]
    ],
];

$aggr = [
    'ua_count' => [
        'terms' => [
            'field' => 'http_user_agent',
            'size' => 10
        ]
    ]
];
        // stats, value_count, min, max, avg, sum
        $request->aggregations($aggr);

// sort documents
/*        $request->sort([
            ['post_date' => ['order' => 'asc']],
            '_score'
        ]);*/

// rescore documents
/*        $request->rescore([
            'window_size' => 50,
            'query' => [
                'rescore_query' => [
                    'match_phrase' => [
                        'message' => [
                            'query' => 'the quick brown',
                            'slop' => 2,
                        ],
                    ],
                ],
                'query_weight' => 0.7,
                'rescore_query_weight' => 1.2,
            ]
        ]);*/

// add a post filter
/*        $request->postFilter([
            'term' => [
                'cover' => 'hard'
            ]
        ]);*/

// track total hits
        $request->trackTotalHits(true);

// track scores
        $request->trackScores(true);

// script fields
/*        $request->scriptFields([
            'my_doubled_field' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => 'doc[params.field] * params.multiplier',
                    'params' => [
                        'field' => 'my_field',
                        'multiplier' => 2,
                    ],
                ],
            ],
        ]);*/

// boost indices
/*        $request->indicesBoost([
            ['my-alias' => 1.4],
            ['my-index' => 1.3],
        ]);*/

// use pagination
        $request->from(0)->size(20);

// execute the search request and get the response
        $response = $documentManager->search($indexName, $request);

// get the total number of matching documents
        $total = $response->total();

// get the corresponding hits
        $hits = $response->hits();

// every hit provides access to the related index name, the score, the document, the highlight and the inner hits
// in addition, you can get a raw representation of the hit
/*        foreach ($hits as $hit) {
            $indexName = $hit->indexName();
            $score = $hit->score();
            $document = $hit->document();
            $highlight = $hit->highlight();
            $innerHits = $hit->innerHits();
            $raw = $hit->raw();
        }*/

        $hit = Arr::first($hits);

// get the suggestions
        $suggestions = $response->suggestions();

// get the aggregations
        $aggregations = $response->aggregations();

        echo "<pre>";
        //print_r($hits);
        //print_r($aggregations);
        foreach($aggregations as $_key => $aggregation) {
            echo $_key . PHP_EOL;
            foreach($aggregation->buckets() as $key => $value) {
                echo "KEY: {$key}" . PHP_EOL;
                echo "VALUE: " . PHP_EOL;
                echo $value->key() . PHP_EOL;
            }
        }
        echo "</pre>";
    }

    public function add()
    {
        $client = \Elasticsearch\ClientBuilder::fromConfig(config('elastic.client'));

        $indexManager = new IndexManager($client);
        $name = 'user_agent_index';
        $indexManager->drop($name);

        if (!$indexManager->exists($name)) {

            $mapping = (new \ElasticAdapter\Indices\Mapping())
                ->text('title', [
                    'boost' => 2,
                ])
                ->keyword('http_user_agent', [
                    'null_value' => 'NULL'
                ])
/*                ->dynamicTemplate('no_doc_values', [
                    'match_mapping_type' => '*',
                    'mapping' => [
                        'type' => '{dynamic_type}',
                        'doc_values' => false,
                    ],
                ])*/;

            $indexBlueprint = new IndexBlueprint($name, $mapping);
            $indexManager->create($indexBlueprint);
        }

        $documentManager = new \ElasticAdapter\Documents\DocumentManager($client);

        $documents = collect([
            new \ElasticAdapter\Documents\Document('1', ['title' => 'Titel 1', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('2', ['title' => 'Titel 2', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('2', ['title' => 'Titel 3', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('3', ['title' => 'Titel 4', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('4', ['title' => 'Titel 5', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('5', ['title' => 'Titel 5', 'http_user_agent' => 'Deezer/6.2.46.xxx (Android; 9; Mobile; de) A-gold BV9700Prod']),
            new \ElasticAdapter\Documents\Document('6', ['title' => 'Titel 6', 'http_user_agent' => 'Podcasts/1629.2.1 CFNetwork/1327.0.4 Darwin/21.2.0']),
        ]);

        //$documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
        $documentManager->index($name, $documents);
    }

    /**
     * @param  array  $range
     * @param  array|null  $sources
     * @param  int  $filesizeMinimum
     * @return \ElasticAdapter\Search\SearchResponse
     */
    public function getShows(array $range, ?array $sources = [], $filesizeMinimum = 2097152)
    {
        $documentManager = self::getDocumentManager();
        $filter = Query::bool()
            ->must(
                Query::bool()
                    ->should(Query::term()
                        ->field('status')->value('200')
                    )
                    ->should(Query::term()
                       ->field('status')->value('206')
                    )
                    ->minimumShouldMatch(1)
            )
            ->must(
                Query::term()
                    ->field('request_method')->value('GET')
            )
            ->mustNot(
                Query::term()
                    ->field('user_agent_details.bot')->value(true)
            )
            ->mustNot(
                Query::term()
                    ->field('user_agent_details.os.name')->value('watchOS')
            )
            ->must(
                Query::range()
                    ->field('timestamp')
                    ->gte($range['df'])
                    ->lte($range['dt'])
            )
            ->must(
                Query::range()
                    ->field('bytes_sent')
                    ->gt($filesizeMinimum)
            );
        // TODO: Multiple feeds have to be "should"
        foreach ($sources as $key => $value) {
            $filter->must(Query::term()->field($key)->value($value));
        }
        $query = Query::bool()->filter($filter)->buildQuery();

//Log::debug(print_r($query, true));

        $searchRequest = new SearchRequest($query);
        $searchRequest->trackTotalHits(true);
        $searchRequest->size(0);
        $calendarInterval = 'day';
        $aggregations = [
/*            'referrer_count' => [
                'terms' => [
                    'field' => 'http_referrer',
                    'size' => 11
                ]
            ],*/
/*            'origin_count' => [
                'terms' => [
                    'field' => 'origin',
                    'size' => 10
                ]
            ],*/
            'country_count' => [
                'filter' => [
                    "term" => [ "status" => "200" ]
                ],
                'aggs' => [
                    'countries' => [
                        'terms' => [
                            'field' => 'geo.country.names.de',
                            'size' => 10
                        ]
                    ]
                ]
            ],
            'client_type_count' => [
                'filter' => [
                    "term" => [ "status" => "200" ]
                ],
                'aggs' => [
                    'client_types' => [
                        'terms' => [
                            'field' => 'user_agent_details.client.type',
                            'size' => 10
                        ]
                    ]
                ]
            ],
            'apps_count' => [
                'filter' => [
                    "term" => [ "status" => "200" ]
                ],
                'aggs' => [
                    'apps' => [
                        'terms' => [
                            'field' => 'user_agent_details.client.type',
                            'size' => 10
                        ],
                        'aggs' => [
                            'clients' => [
                                'terms' => [
                                    'field' => 'user_agent_details.client.name',
                                    'size' => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ],
/*            'os_count' => [
                'terms' => [
                    'field' => 'user_agent_details.os.name',
                    'size' => 10
                ]
            ],*/
            'osfamily_count' => [
                'filter' => [
                    "term" => [ "status" => "200" ]
                ],
                'aggs' => [
                    'operating_systems' => [
                        'terms' => [
                            'field' => 'user_agent_details.os.family',
                            'size' => 10
                        ]
                    ]
                ]
            ],
            'unique_listeners' => [
                'cardinality' => [
                    'field' => 'ip_hash'
                ]
            ],
            'listeners_over_time' => [
                'date_histogram' => [
                    'field' => 'time',
                    'calendar_interval' => $calendarInterval,
                    "format" => "yyyy-MM-dd"
                ],
                'aggs' => [
                    'distinct_listeners' => [
                        'cardinality' => [
                            'field' => 'ip_hash'
                        ]
                    ]
                ]
             ],
            'downloads_over_time_per_media' => [
                'filter' => [
                    "term" => [ "status" => "200" ]
                ],
                'aggs' => [
                    'downloads' => [
                        'date_histogram' => [
                            'field' => 'timestamp',
                            'calendar_interval' => $calendarInterval,
                            "format" => "yyyy-MM-dd"
                        ],
                        'aggs' => [
                            'medias' => [
                                'terms' => [
                                    'field' => 'episode_id',
                                    'size' => 5,
                                ],
                            ],
                        ]
                    ]
                ]
             ],
/*            'hits_over_time_per_status' => [
                'date_histogram' => [
                    'field' => 'time',
                    'calendar_interval' => $calendarInterval,
                    "format" => "yyyy-MM-dd"
                ],
                'aggs' => [
                    'statuses' => [
                        'terms' => [
                            'field' => 'status',
                            'size' => 5,
                        ],
                    ],
                    "aggs"=> [
                        "terms"=> [
                            "field"=> "episode_id",
                        ]
                    ]
                ],
            ],*/
/*
            'hits_over_time_for_media' => [
                "aggs" => [
                    "group_by" => [
                        "terms" => [
                            "field" => "episode_id"
                        ]
                    ]
                ]
                "aggs" => [
                    "episodeId" => [
                        "terms" => [
                            "field" => "episode_id",
                            "include" => [
                                $sources['episode_id']
                            ]
                        ]
                    ],
                    "aggs"=> [
                        "value"=> [
                            "range"=> [
                                "field"=> "time",
                                "ranges"=> [
                                    [
                                        "from"=> "2022-01-01",
                                        "to"=> "2022-03-01"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],*/
/*            "download_types" => [
                "filters" => [
                    "filters" => [
                        "downloads" => [
                            "match" => [ "status" => "200" ]
                        ],
                        "streams" => [
                            "match" => [ "status" => "206" ]
                        ]
                    ]
                ]
            ],*/

/*            'unique_count' => [
                'aggs' => [
                    "streams" => [
                        "terms" => [
                            "field" => "status",
                            "size" => 10,
                        ],
                        "aggs" => [
                            "streams_by_ip" => [
                                "terms" => [
                                    "field" => "ip_hash",
                                    "size" => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ]*/

            "streams_over_time_per_media" => [
                'filter' => [
                    "term" => [ "status" => "206" ]
                ],
                'aggs' => [
                    'streams' => [
                        'date_histogram' => [
                            'field' => 'time',
                            'calendar_interval' => $calendarInterval,
                            "format" => "yyyy-MM-dd"
                        ],
                        'aggs' => [
                            "streams" =>
                            [
                                "multi_terms" => [
                                    "size" => "65536",
                                    "terms" => [
                                        ["field" => "media"],
                                        ["field" => "ip_hash"],
                                        ["field" => "http_user_agent"],
                                    ]
                                ],
                                "aggs" => [
                                    "transferred" => [
                                        "sum" => [
                                            "field" => "bytes_sent"
                                        ]
                                    ],
                                    "threshold" => [
                                        "bucket_selector" => [
                                            "buckets_path" => [
                                                "total" => "transferred"
                                            ],
                                            "script" => "params.total > {$filesizeMinimum}"
                                        ]
                                    ]
                                ]
                            ],
                            "valid_streams" => [
                                "stats_bucket" => [
                                    "buckets_path" => "streams>transferred"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

/*        "aggs": {
        "streams": {
            "multi_terms": {
                "size": "65536",
                "terms": [
                    {
                        "field": "media"
                    },
                    {
                        "field": "ip_hash"
                    },
                    {
                        "field": "http_user_agent"
                    }
                ]
            },
            "aggs": {
                "transferred": {
                    "sum": {
                        "field": "bytes_sent"
                    }
                },
                "threshold": {
                    "bucket_selector": {
                        "buckets_path": {
                            "total": "transferred"
                        },
                        "script": "params.total > 12345"
                    }
                }
            }
        },
        "valid_streams": {
            "stats_bucket": {
                "buckets_path": "streams>transferred"
            }
        }
    }*/
        $searchRequest->aggregations($aggregations);

        return $documentManager->search(self::INDEX, $searchRequest);
    }

    /**
     * @return \ElasticAdapter\Documents\DocumentManager|mixed
     */
    public static function getDocumentManager()
    {
        static $documentManager;

        if (!$documentManager) {
            $client = \Elasticsearch\ClientBuilder::fromConfig(config('elastic.client'));
            $documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
        }

        return $documentManager;
    }
}
