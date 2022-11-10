<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Horizon\Horizon;
use App\Models\User;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Localization Carbon
        setlocale(LC_TIME, 'de_DE.utf8');
        \Carbon\Carbon::setLocale(config('app.locale'));

        $pUrl = env('PROXY_URL');

        if (!empty($pUrl)) {
            URL::forceRootUrl($pUrl);
        }

        $pScheme = env('PROXY_SCHEMA');

        if (!empty($pScheme)) {
            URL::forceScheme($pScheme);
        }

        Horizon::auth(function ($request) {
            return auth()->check()
                && auth()->user()->role_id === User::ROLE_ADMIN;
        });

        Paginator::useBootstrap();

        //Validator::extend('storagespace', 'StorageSpaceValidator@validate');
        //Validator::replacer('storagespace', 'StorageSpaceValidator@message');

        /**
         * Somehow PHP is not able to write in default /tmp directory and SwiftMailer was failing.
         * To overcome this situation, we set the TMPDIR environment variable to a new value.
         */
        if (class_exists('Swift_Preferences')) {
            \Swift_Preferences::getInstance()->setTempDir(storage_path() . '/tmp');
        }

/*        DB::listen(function ($query) {
            Log::debug($query->sql);
            // $query->sql
            // $query->bindings
            // $query->time
        });*/

        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    return (new LengthAwarePaginator(
                        $this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))
                        ->withPath('');
                });
        }

        Blade::directive('inline', function ($expression) {
            $include = implode("\n", [
                "/* {$expression} */",
                "<?php include public_path({$expression}) ?>\n",
            ]);

            if (Str::endsWith($expression, ".html'")) {
                return $include;
            }

            if (Str::endsWith($expression, ".css'")) {
                return "<style>\n".$include.'</style>';
            }

            if (Str::endsWith($expression, ".js'")) {
                return "<script>\n".$include.'</script>';
            }
        });

        Passport::hashClientSecrets();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'development') {
            $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
            //$this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            //$this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }
        // mb4 encoding compat
        //Schema::defaultStringLength(191);
    }
}
