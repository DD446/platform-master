<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\FaqPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Models\Package;
use App\Policies\CampaignInvitationPolicy;
use App\Policies\CampaignPolicy;
use App\Policies\FeedPolicy;
use App\Policies\PlayerConfigPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * Policies that follow standards are autodiscovered
     * @see https://laravel.com/docs/8.x/authorization#policy-auto-discovery
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        $defaultScopes = [
            'read-only-user',
            'read-only-feeds',
            'read-only-shows',
            // 'embed-player',
        ];

        Passport::setDefaultScope($defaultScopes);

        Passport::tokensCan([
            'feeds' => trans('Manage feeds'),
            'read-only-feeds' => trans('Retrieve feeds'),
            'shows' => trans('Manage shows'),
            'read-only-shows' => trans('Retrieve shows'),
            'media' => trans('Manage media'),
            'read-only-media' => trans('Retrieve media'),
            'user' => trans('Manage user data'),
            'read-only-user' => trans('Retrieve user data'),
            'read-only-stats' => trans('Retrieve stats'),
            'read-only-teams' => trans('Retrieve teams'),
            'teams' => trans('Manage teams'),
            'read-only-members' => trans('Retrieve members'),
            'members' => trans('Manage members'),
        ]);

        Gate::define('viewWebSocketsDashboard', function ($user) {
            return $user->role_id === User::ROLE_ADMIN;
        });

        Gate::define('paymentStoreBill', function ($user) {
            return $user->can_pay_by_bill;
        });

        Gate::define('hasAuphonicFeature', function ($user) {
            return has_package_feature($user->package, Package::FEATURE_AUPHONIC);
        });

        Gate::define('viewPlayerConfigurator', function ($user) {
            return has_package_feature($user->package, Package::FEATURE_PLAYER);
        });

        Gate::define('canSavePlayerConfigurations', function ($user) {
            return has_package_feature($user->package, Package::FEATURE_PLAYER_CONFIGURATION);
        });

        Gate::define('canUseCustomStylesForPlayer', function ($user) {
            return has_package_feature($user->package, Package::FEATURE_PLAYER_CUSTOMSTYLES);
        });

        Gate::define('deleteDuplicateShow', function ($user) {
            return $user->role_id === User::ROLE_ADMIN;
        });

        Gate::define('viewBillsInBackup', function ($user) {
            if (in_array($user->email, ['gastzugang@podcast.de'])) {
                return true;
            }

            return $user->role_id === User::ROLE_ADMIN;
        });

        Gate::define('viewFeeds', function ($user) {
            return in_array($user->role_id, [User::ROLE_USER, User::ROLE_SUPPORTER, User::ROLE_EDITOR]);
        });

        Gate::define('viewMedia', function ($user) {
            return in_array($user->role_id, [User::ROLE_USER, User::ROLE_SUPPORTER, User::ROLE_EDITOR]);
        });

        Gate::define('viewStats', function ($user) {
            return in_array($user->role_id, [User::ROLE_USER, User::ROLE_SUPPORTER, User::ROLE_EDITOR]);
        });

        Gate::define('viewSettings', function ($user) {
            return in_array($user->role_id, [User::ROLE_USER, User::ROLE_SUPPORTER, User::ROLE_EDITOR]);
        });

/*        Gate::before(function ($user, $ability) {
            if ($user instanceof \Statamic\Auth\Eloquent\User && $user->isSuper()) {
                return true;
            }
        });*/
    }

    public function register()
    {
        parent::register();

        \Illuminate\Support\Facades\Auth::provider('md5userprovider', function($app, array $config) {
            return new MD5UserProvider($app['hash'], $config['model']);
        });
    }
}
