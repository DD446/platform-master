<?php

namespace App\Providers;

use App\Nova\Dashboards\AdminInsights;
use App\Nova\Dashboards\Agency;
use App\Nova\Dashboards\PageAnalytics;
use App\Nova\Metrics\AudiotakesPioniereCount;
use App\Nova\Metrics\ChangeCount;
use App\Nova\Metrics\ContactUsCount;
use App\Nova\Metrics\FaqCount;
use App\Nova\Metrics\FeedbackCount;
use App\Nova\Metrics\NewsCount;
use App\Nova\Metrics\PlayerConfigTrend;
use App\Nova\Metrics\ReviewCount;
use App\Nova\Metrics\SupporterCount;
use App\PioniereManagement\PioniereManagement;
use App\SaasMetrics\SaasMetrics;
use Audiotakes\PioniereCheckManagement\PioniereCheckManagement;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\FundsManagement\FundsManagement;
use App\Models\User;
use App\Nova\Dashboards\Payments;
use App\Nova\Dashboards\UserInsights;
use App\Nova\Metrics\UsersPerDay;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

/*        Nova::userTimezone(function (Request $request) {
            return $request->user()->timezone;
        });*/
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                /*->withPasswordResetRoutes()
                ->register()*/;
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {

            if (!in_array($user->role_id, [
                    User::ROLE_ADMIN,
                    User::ROLE_EDITOR,
                    User::ROLE_SUPPORTER,
                ])
                && $user->email != 'bastian@audiotakes.net'
                && !$user->agency_enabled
            ) {
                abort(redirect()->route('home'));
            }

            return in_array($user->email, [
                'info1@open-haus.de',
                'steffen.supporter@podcast-plattform.de',
                'max.hurlebaus@podcast.de',
                'steffen@podcaster.de', # Editor
                'niklas@podcaster.de',
                'max.supporter@podcast.de',
                'www.podcast.de@googlemail.com', # Supporter
                'kino-aktuell@podcast.de', # Editor
                'bastian@audiotakes.net',
            ]) || $user->agency_enabled;
        });

        Gate::define('viewNovaDashboardAdminInsights', function ($user) {
            return in_array($user->role_id, [User::ROLE_ADMIN]);
        });

        Gate::define('viewNovaDashboardPageAnalytics', function ($user) {
            return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR]);
        });

        Gate::define('viewNovaDashboardUserInsights', function ($user) {
            return in_array($user->role_id, [User::ROLE_ADMIN]);
        });

        Gate::define('viewNovaDashboardPayments', function ($user) {
            return in_array($user->role_id, [User::ROLE_ADMIN]);
        });

        Gate::define('viewNovaDashboardAgency', function ($user) {
            return in_array($user->role_id, [User::ROLE_ADMIN]) || $user->agency_enabled;
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        if (auth()->user()->agency_enabled) {
            return [
                new SupporterCount,
            ];
        }

        return [
            new UsersPerDay,
            new ContactUsCount,
            new NewsCount,
            new FeedbackCount,
            new ReviewCount,
            new FaqCount,
            new ChangeCount,
            new AudiotakesPioniereCount,
            new PlayerConfigTrend,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (new FundsManagement)->canSee(function ($request) {
                if (auth()->user()->role_id === User::ROLE_ADMIN) {
                    return true;
                }

                if (in_array(auth()->user()->email, ['niklas@podcaster.de'])) {
                    return true;
                }

                return false;
            }),
            (new PioniereManagement)->canSee(function ($request) {
                if (auth()->user()->role_id === User::ROLE_ADMIN) {
                    return true;
                }

                if (in_array(auth()->user()->email, ['bastian@audiotakes.net'])) {
                    return true;
                }

                return false;
            }),
            (new SaasMetrics)->canSee(function ($request) {
                if (auth()->user()->role_id === User::ROLE_ADMIN) {
                    return true;
                }

                return false;
            }),
            (new \BinaryBuilds\NovaAdvancedCommandRunner\CommandRunner)->canSee(function ($request) {
                if (auth()->user()->role_id === User::ROLE_ADMIN) {
                    return true;
                }

                return false;
            }),
            new PioniereCheckManagement()
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            (new AdminInsights)->canSeeWhen('viewNovaDashboardAdminInsights', User::class),
            (new UserInsights)->canSeeWhen('viewNovaDashboardUserInsights', User::class),
            (new Payments)->canSeeWhen('viewNovaDashboardPayments', User::class),
            (new PageAnalytics())->canSeeWhen('viewNovaDashboardPageAnalytics', User::class),
            //(new Agency())->canSeeWhen('viewNovaDashboardAgency', User::class),
        ];
    }
}
