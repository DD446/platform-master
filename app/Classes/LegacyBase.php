<?php
/**
 * User: fabio
 * Date: 30.08.19
 * Time: 12:08
 */

namespace App\Classes;

class LegacyBase
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * Legacy constructor.
     */
    public function __construct()
    {
        if (env('APP_ENV') === 'production') {
            $url = config('app.url');

            if (strpos($url, 'stage') !== false) {
                $rootDir = '/var/www/stage.podcaster.de/seagull';
            } elseif(strpos($url, 'devel') !== false) {
                $rootDir = '/var/www/devel.podcaster.de/seagull';
            } elseif(strpos($url, 'sattoaster') !== false) {
                $rootDir = '/home/fabio/public_html/www.podcaster.de';
            } else {
                $rootDir = '/var/www/www.podcaster.de/portal';
            }
        } else {
            $rootDir = '/home/fabio/public_html/www.podcaster.de';
        }
        require_once $rootDir . '/lib/SGL/FrontController.php';
        require_once $rootDir . '/lib/SGL/Session.php';
        define('SGL_CACHE_LIBS', false);
        define('SGL_INSTALLED', true);
        LoginLegacyInit::run();

        $this->rootDir = $rootDir;
    }
}
