<?php
/**
 * User: fabio
 * Date: 05.08.21
 * Time: 11:19
 */

namespace App\Classes\Adswizz;

class QueryBuilder
{
    protected $interval = [];
    protected $metrics = [];
    protected $filters = [];
    protected $splitters = [];
    protected $sort = ["dir" => "DESC"];
    protected $timezone = 'Europe/Berlin';
    protected $limit = 25;

    private $allowedFilters = [
        "publisherName"
    ];

    private $allowedMetrics = [
        "objectiveCountableSum",
        "listenerIdHLL",
        "bidWons",
        "supplyInsertionRate",
        "fillRate",
        "supplyRevenueInUSD",
        "inventory"
    ];

    /**
     * @return array
     */
    public function getInterval(): array
    {
        return $this->interval;
    }

    /**
     * @param  array  $interval
     */
    public function setInterval(array $interval): void
    {
        if (!isset($interval['from']) || !$interval['from']) {
            throw new \Exception('Missing attribute "from" for interval');
        }

        if (!strtotime($interval['from'])) {
            throw new \Exception('Attribute "from" for interval must be a valid time');
        }

        if (!isset($interval['to']) || !$interval['to']) {
            throw new \Exception('Missing attribute "to" for interval');
        }

        if (!strtotime($interval['to'])) {
            throw new \Exception('Attribute "to" for interval must be a valid time');
        }

        $this->interval = $interval;
    }

    public function get(): array
    {
        return [
            "interval" => $this->interval,
            "metrics" => $this->metrics,
            "filters" => $this->filters,
            "splitters" => $this->splitters,
            "sort" => $this->sort,
            "timezone" => $this->timezone
        ];
    }

    /**
     * @return array
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * @param  array  $metrics
     */
    public function setMetrics(array $metrics): void
    {
        foreach ($metrics as $metric) {
            if (!in_array($metric, $this->metrics)) {
                if (in_array($metric, $this->allowedMetrics)) {
                    $this->addMetric($metric);
                }
            }
        }
    }

    public function addMetric(string $metric)
    {
        array_push($this->metrics, $metric);
    }

    /**
     * @param  array  $filter
     */
    public function addFilter(array $filter)
    {
        array_push($this->filters, $filter);
    }

    /**
     * @param $publisher
     * @param boolean $exclusion Be careful to set this to true as it returns data for all publishers except the given one
     * @return void
     */
    public function setPublisher($publisher, $exclusion = false)
    {
        $this->addFilter([
            'id' => "publisherName",
            'values' => (array)$publisher,
            'exclusion' => $exclusion
        ]);
    }

    /**
     * @param  string  $type
     * @param  string  $direction
     * @return void
     */
    public function setSort(string $type, string $direction = 'DESC')
    {
        $this->sort = [
            'id' => $type,
            'dir' => $direction
        ];
    }

    /**
     * @return array
     */
    public function getSplitters(): array
    {
        return $this->splitters;
    }

    /**
     * @param  array  $splitters
     */
    public function setSplitters(array $splitters): void
    {
        foreach ($splitters as $splitter) {
            $this->addSplitter($splitter);
        }
    }

    /**
     * @param  array  $splitter
     */
    public function addSplitter(array $splitter)
    {
        array_push($this->splitters, $splitter);
    }

    /**
     * @param  int  $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
