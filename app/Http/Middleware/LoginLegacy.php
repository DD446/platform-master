<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Classes\LoginLegacyInit;


class LoginLegacy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (env('APP_ENV') === 'testing') {
            return $next($request);
        }

        $response = $next($request);

        // Perform action
        if (Auth::guard($guard)->check()) {
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
            $id = Auth::guard($guard)->user()->usr_id;
            LoginLegacyInit::run();
            //Log::debug("Config : " . \SGL_Config::singleton()->getFileName());
            new \SGL_Session($id, true);
        }

        return $response;
    }
}
