<?php
/**
 * User: fabio
 * Date: 20.02.22
 * Time: 15:45
 */

namespace App\Classes;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

class UserAgent
{
    private DeviceDetector $deviceDetector;

    public function __construct()
    {
        $this->deviceDetector = new DeviceDetector();
    }

    private function setUserAgent(string $userAgent)
    {
        $this->deviceDetector->setUserAgent($userAgent);
        $this->deviceDetector->parse();
    }

    public function getData(string $userAgent)
    {
        $this->setUserAgent($userAgent);

        if ($this->deviceDetector->isBot()) {
            $data = $this->deviceDetector->getBot();
            $data['name'] = $userAgent;
            $data['bot'] = true;

            return $data;
        }

        $browser = $this->deviceDetector->getClient();

        if (is_array($browser)) {
            $browser['family'] = Browser::getBrowserFamily($this->deviceDetector->getClient('name'));
        }

        $os = $this->deviceDetector->getOs();

        if (is_array($os)) {
            $os['family'] = OperatingSystem::getOsFamily($this->deviceDetector->getOs('name'));
        }

        return [
            'bot' => false,
            'name' => $userAgent,
            'useragent' => $this->deviceDetector->getUserAgent(),
            'brandname' => $this->deviceDetector->getBrandName(),
            'devicetype' => [
                'browser' => $this->deviceDetector->isBrowser(),
                'smartphone' => $this->deviceDetector->isSmartphone(),
                'smartspeaker' => $this->deviceDetector->isSmartSpeaker(),
                'smartdisplay' => $this->deviceDetector->isSmartDisplay(),
                'tv' => $this->deviceDetector->isTV(),
                'carbrowser' => $this->deviceDetector->isCarBrowser(),
                'camera' => $this->deviceDetector->isCamera(),
                'console' => $this->deviceDetector->isConsole(),
                'featurephone' => $this->deviceDetector->isFeaturePhone(),
                'feedreader' => $this->deviceDetector->isFeedReader(),
                'mediaplayer' => $this->deviceDetector->isMediaPlayer(),
                'portablemediaplay' => $this->deviceDetector->isPortableMediaPlayer(),
                'mobileapp' => $this->deviceDetector->isMobileApp(),
                'mobile' => $this->deviceDetector->isMobile(),
                'peripheral' => $this->deviceDetector->isPeripheral(),
                'phablet' => $this->deviceDetector->isPhablet(),
                'pim' => $this->deviceDetector->isPIM(),
                'desktop' => $this->deviceDetector->isDesktop(),
                'touchenabled' => $this->deviceDetector->isTouchEnabled(),
                'tablet' => $this->deviceDetector->isTablet(),
                'wearable' => $this->deviceDetector->isWearable(),
            ],
            'client' => $browser,
            'device' => [
                'id' => $this->deviceDetector->getDevice(),
                'name' => $this->deviceDetector->getDeviceName(),
            ],
            'model' => $this->deviceDetector->getModel(),
            'os' => $os,
        ];
    }
}
