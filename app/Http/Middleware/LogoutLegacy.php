<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LogoutLegacy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') === 'testing') {
            return $next($request);
        }

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
        LogoutLegacyInit::run();
        //require_once $rootDir . '/modules/user2/classes/Login2Mgr.php';
        //$l = new \Login2Mgr();
        //$l->_cmd_logout(\SGL_Registry::singleton(), new \SGL_Output());

        $id = Auth::user()->usr_id;

        list(, $cookieValue) = @unserialize($_COOKIE['SGL_REMEMBER_ME']);
        if (!empty($cookieValue)) {
            require_once $rootDir . '/modules/user/classes/UserDAO.php';
            \UserDAO::singleton()->deleteUserLoginCookieByUserId($id, $cookieValue);
        }
        \SGL_Session::destroy();

        return $next($request);
    }
}

class LogoutLegacyInit/* extends \SGL_FrontController*/
{
    public static function run()
    {
        if (!defined('SGL_INITIALISED')) {
            \SGL_FrontController::init();
        }
        //  assign request to registry
        $input = \SGL_Registry::singleton();
        $req = \SGL_Request::singleton();

        $input->setRequest($req);

        $outputClass = \SGL_FrontController::getOutputClass();
        $output = new $outputClass();

        // run module init tasks
        \SGL_Task_InitialiseModules::run();

        $process =  new \SGL_Task_Init(
            new \SGL_Task_DiscoverClientOs(
            /*new SGL_Task_SetupLangSupport2(*/
                new \SGL_Void()
            /*)*/));

        $process->process($input, $output);
    }
}
