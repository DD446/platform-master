<?php
/**
 * User: fabio
 * Date: 28.05.21
 * Time: 17:24
 */

namespace App\Classes\Adswizz;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DomainApiManager
{
    const DOMAIN_API_VERSION = 7;

    const URL = 'https://api.adswizz.com/domain/v' . self::DOMAIN_API_VERSION;

    private $apiKey;

    /**
     * AdswizzDomainApiManager constructor.
     */
    public function __construct()
    {
        $this->apiKey = env('DOMAIN_API_KEY');
    }

    protected function getKey(): string
    {
        if (!$this->apiKey) {
            throw new \Exception('API key is not configured. Please provide `DOMAIN_API_KEY` in .env file.');
        }

        return $this->apiKey;
    }

    /**
     * @param  string  $path
     * @param  array  $headers
     * @param  array|null  $params
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws \Exception
     */
    protected function _get(string $path, array $headers = [], ?array $params = [])
    {
        $headers = array_merge(['x-api-key' => $this->getKey()], $headers);
        $params = http_build_query($params);

        return Http::withHeaders($headers)
            ->get(self::URL . DIRECTORY_SEPARATOR . $path . '?'  . $params);
    }

    protected function get(string $path, ?array $headers = [], ?array $params = [])
    {
        $result = $this->_get($path, $headers, $params);

        if (!$result->successful()) {
            throw new \Exception($result->body());
        }

        return  json_decode($result->body());
    }

    protected function _post(string $path, array $headers = [], ?array $data = [])
    {
        $headers = array_merge(['x-api-key' => $this->getKey()], $headers);

        return Http::withHeaders($headers)
            ->post(self::URL . DIRECTORY_SEPARATOR . ltrim($path, '/'), $data);
    }

    /**
     * @param  string  $path
     * @param  array|null  $headers
     * @param  array|null  $params
     * @return mixed
     * @throws \Exception
     */
    protected function post(string $path, ?array $headers = [], ?array $params = [])
    {
        $result = $this->_post($path, $headers, $params);

        if (!$result->successful()) {
            throw new \Exception($result->body());
        }

        return  json_decode($result->body());
    }

    public function getAgencies()
    {
        return $this->get('agencies');
    }

    public function getSiteChannels(?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('site-channels', ['agency' => $agency->id]);
    }

    public function getOurAgency()
    {
        $agencies = $this->get('agencies');

        return $agencies[0];
    }

    public function getCategories(int $limit = 100, int $page = 1, ?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('categories', ['agency' => $agency->id], ['limit' => $limit, 'page' => $page]);
    }

    public function getSubcategories(int $limit = 100, int $page = 1, ?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('categories/subcategories', ['agency' => $agency->id], ['limit' => $limit, 'page' => $page]);
    }

    public function getPublishers(int $limit = 100, int $page = 1, ?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('publishers', ['agency' => $agency->id], ['limit' => $limit, 'page' => $page]);
    }

    /**
     * @param  int  $id
     * @param  \stdClass|null  $agency
     * @return object
     * @throws \Exception
     */
    public function getPublisher(int $id, ?\stdClass $agency = null): object
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('publishers/' . $id, ['agency' => $agency->id]);
    }

    public function getDmpPodcastContexualGroups(?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/podcast/contextual', ['agency' => $agency->id]);
    }

    public function getDmpPodcastContexualValues(\stdClass $contextualGroup, ?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/podcast/contextual/' . $contextualGroup->code, ['agency' => $agency->id]);
    }

    public function getDmpPodcastConsumptionGroups(?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/podcast/consumption', ['agency' => $agency->id]);
    }

    public function getDmpPodcastConsumptionValues(\stdClass $consumptionGroup, ?\stdClass $agency = null): array
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/podcast/consumption/' . $consumptionGroup->code, ['agency' => $agency->id]);
    }

    public function getDmpPublishers(?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/publisher', ['agency' => $agency->id]);
    }

    public function getDmpPublisherValues(\stdClass $publisher, ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/publisher/' . $publisher->id, ['agency' => $agency->id]);
    }

    public function getDmpProviders(?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp', ['agency' => $agency->id]);
    }

    public function getAudienceSegments(\stdClass $dmp, ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->get('commons/dmp/' . $dmp->code, ['agency' => $agency->id]);
    }

    public function getDeviceTypesAndManufacturers(?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }
        return $this->get('commons/device-manufacturers', ['agency' => $agency->id]);
    }

    public function getOpenrtbBuyers(?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }
        return $this->get('commons/openrtb-buyers', ['agency' => $agency->id]);
    }

    public function getFilter(array $query, $environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        $environments = ['AUDIOSERVE', 'AUDIOMAX'];
        if (!in_array($environment, $environments)) {
            throw new \Exception('Environment is not in list of allowed ones (' . implode(',' , $environments) . ').');
        }

        return $this->post('reports/filter', ['agency' => $agency->id, 'environment' => $environment], $query);
    }

    public function getMetrics($environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        $environments = ['AUDIOSERVE', 'AUDIOMAX'];
        if (!in_array($environment, $environments)) {
            throw new \Exception('Environment is not in list of allowed ones (' . implode(',' , $environments) . ').');
        }

        return $this->get('reports/metrics', ['agency' => $agency->id, 'environment' => $environment]);
    }

    public function getDimensions($environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        $environments = ['AUDIOSERVE', 'AUDIOMAX'];
        if (!in_array($environment, $environments)) {
            throw new \Exception('Environment is not in list of allowed ones (' . implode(',' , $environments) . ').');
        }

        return $this->get('reports/dimensions', ['agency' => $agency->id, 'environment' => $environment]);
    }

    /**
     * @param  int  $limit
     * @param  int  $page
     * @param  string|null  $modifiedAfter <date-time> Please note that, when using lastModified date, the result will include only the changes made to that entity from that date on. It will not include the creation or update of a child of that entity.Format: yyyy-MM-dd'T'HH:mm:ssZ
     * @param  string  $environment
     * @param  \stdClass|null  $agency
     * @return mixed
     * @throws \Exception
     */
    public function getSupplyPacks(int $limit = 100, int $page = 1, string $modifiedAfter = null, $environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        $environments = ['AUDIOSERVE', 'AUDIOMAX'];
        if (!in_array($environment, $environments)) {
            throw new \Exception('Environment is not in list of allowed ones (' . implode(',' , $environments) . ').');
        }

        return $this->get('supply-packs', ['environment' => $environment, 'agency' => $agency->id], compact('limit', 'page', 'modifiedAfter'));
    }

    public function getSupplyPack(int $supplyPackId, $environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        $environments = ['AUDIOSERVE', 'AUDIOMAX'];
        if (!in_array($environment, $environments)) {
            throw new \Exception('Environment is not in list of allowed ones (' . implode(',' , $environments) . ').');
        }

        return $this->get("supply-packs/{$supplyPackId}", ['environment' => $environment, 'agency' => $agency->id], compact('supplyPackId'));
    }

    public function queryAnalytics(array $data, $environment = 'AUDIOMAX', ?\stdClass $agency = null)
    {
        if (is_null($agency)) {
            $agency = $this->getOurAgency();
        }

        return $this->post('reports/query', ['environment' => $environment, 'agency' => $agency->id], $data);
    }

    private function getCachedPublishers()
    {
        $publishers = Cache::get('ADSWIZZ_PUBLISHERS', function () {
            $publishers = [];
            // Adjust if there are more than 1K publishers/podcasts
            for($i = 1; $i <= 10; $i++) {
                $_publishers = $this->getPublishers(500, $i);
                if (count($_publishers) === 0) {
                    break;
                }
                $publishers = array_merge($publishers, $_publishers);
            }
            Cache::forever("ADSWIZZ_PUBLISHERS", $publishers);

            return $publishers;
        });

        return $publishers;
    }

    /**
     * @param  string  $publisherName
     * @return mixed
     * @throws \Exception
     */
    public function getPublisherDetails(string $publisherName)
    {
        $publishers = $this->getCachedPublishers();

        foreach ($publishers as $publisher) {
            if ($publisher->externalref && $publisher->externalref == $publisherName) {
                return $publisher;
            }
        }

        throw new NoPublisherFoundException("No publisher found for '{$publisherName}'");
    }

    /**
     * @param  string  $publisherName
     * @return mixed
     * @throws \Exception
     */
    public function getPublisherId(string $publisherName)
    {
        $publisher = $this->getPublisherDetails($publisherName);

        return $publisher->id;
    }
}
