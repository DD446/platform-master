<?php
/**
 * User: fabio
 * Date: 09.03.18
 * Time: 10:42
 */

namespace App\Classes;


class HashServiceProvider extends \Illuminate\Hashing\HashServiceProvider
{
    public function register()
    {
        $this->app->singleton('hash', function ($app) {
            return new HashManager($app);
        });

        $this->app->singleton('hash.driver', function ($app) {
            return $app['hash']->driver();
        });
    }
}
