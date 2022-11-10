# 2-FACTOR AUTHENTICATION (2FA)

------

## FORTIFY

* Official documentation: https://laravel.com/docs/9.x/fortify#what-is-fortify


* Add Fortify package: `composer require laravel/fortify`
* Publish Fortify's resources: `php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"`
* Register newly published Provider in `config/app.php`: add `App\Providers\FortifyServiceProvider::class,`
* Register needed Features by (un)commenting them in `config/fortify.php` in array `features => [...]`
* Run migration: `php artisan migrate`
* In FortifyServiceProvider, (un)comment all features as needed or not needed
* Add Login and Two Factor Auth routes to FortifyServiceProvider:

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.twofactor');
        });

        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });

* Create new blade view `auth/twofactor`
* Add new method for two-factor-authentication to LoginController, something like:

      public function twoFactorAuth(){
        return view('auth.twoFactor');
      }

### TWO FACTOR AUTHENTICATION (2FA)

* Follow these steps:

      https://laravel.com/docs/8.x/fortify#two-factor-authentication

### NEED TO ADD THESE BUGFIXES

###### MAKE FORTIFY WORK

* Fortify only works, if you also activate the Fortify Login Routing instead of your custom one:
  * Remove your custom login route in `web.php`
  * Add the login route in `FortifyServiceProvider`:

        Fortify::loginView(function () {
          return view('auth.login');
        });


###### CORRECTLY DISABLE 2FA

* To disable the 2FA, you need to manually set the user-db-field `two_factor_confirmed_at` to null, because Fortify doesn't do that automatically
(see https://dev.to/nicolus/laravel-fortify-implement-2fa-in-a-way-that-won-t-let-users-lock-themselves-out-2ejk ):
* Override the vendor class `DisableTwoFactorAuthentication` by creating a new class like that:

        <?php
        
        namespace App\Actions\Fortify;
        
        class DisableTwoFactorAuthentication extends \Laravel\Fortify\Actions\DisableTwoFactorAuthentication
        {
            public function __invoke($user)
            {
                $user->forceFill([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])->save();
            }
        }

* Then, register that new class in the FortifyServiceProvider (for Laravel 8 this way):

        public function register()
        {
            $this->app->singleton(
                \Laravel\Fortify\Actions\DisableTwoFactorAuthentication::class,
                \App\Actions\Fortify\DisableTwoFactorAuthentication::class
            );
        }


###### DO NOT LOCK USERS OUT OF THEIR OWN ACCOUNT

* On Login, Fortify just checks if the user's db field `two_factor_secret` is set
  => if yes, Fortify will do the redirect to the 2fa
* This way, users are locked out of their accounts if they haven't actually finished their activation process for 2fa
* Fortify also needs to check if the user's db field `two_factor_confirmed_at` is set as well => and only then redirect to the 2fa form
* So, create a new class `RedirectIfTwoFactorConfirmed.php` that extends the vendor class `RedirectIfTwoFactorAuthenticatable.php` with the content:

        <?php
        
        namespace App\Actions\Fortify;
        
        use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
        use Laravel\Fortify\TwoFactorAuthenticatable;
        
        class RedirectIfTwoFactorConfirmed extends RedirectIfTwoFactorAuthenticatable
        {
            public function handle($request, $next)
            {
                $user = $this->validateCredentials($request);
        
                if (optional($user)->two_factor_confirmed_at &&
                    in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                    return $this->twoFactorChallengeResponse($request, $user);
                }
        
                return $next($request);
            }
        }

* Then, change the settings for the pipe used inside the `FortifyServiceProvider`:

      Fortify::authenticateThrough(function (Request $request) {
        return array_filter([
          config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
          Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorConfirmed::class : null,
          AttemptToAuthenticate::class,
          PrepareAuthenticatedSession::class,
        ]);
      });

=> The only difference here is the exchange of the call of `RedirectIfTwoFactorAuthenticatable` with `RedirectIfTwoFactorConfirmed`
