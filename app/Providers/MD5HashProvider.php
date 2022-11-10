<?php
/**
 * User: Fabio Bacigalupo <f.bacigalupo@open-haus.de>
 * Date: 19.10.17
 * Time: 09:13
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\MD5Hasher;

class MD5HashProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->app->singleton('hash', function () {
            return new MD5Hasher;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hash'];
    }
}
