<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest'])->except(['logout', 'show']);
        $this->middleware(['after.login'])->only(['login']);
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $res = $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());

        if (!$this->guard()->user()->password) {
            $this->guard()->user()->update(['password' => password_hash($request->get('password'), PASSWORD_BCRYPT)]);
        }

        return $res;
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function showLoginForm()
    {
        \SEO::setTitle(trans('login.page_title_login'));
        \SEO::setDescription(trans('login.page_description_login', ['name' => config('app.name')]));

        return view('auth.login', ['hideNav' => true]);
    }


    public function show($id)
    {
        return Hashids::connection('hashed_passport')->encode($id);
    }

    public function showSocialLogin($name)
    {
        $hideNav = true;

        //return view('auth.social', compact('name', 'hideNav'));
        return Socialite::driver($name)
            ->with(['social_type' => $name])
            ->asPopup()
            ->redirect();
    }

    public function socialLogin(Request $request)
    {
        $socialType = $request->name;

        if ($request->is('api/*')) {
            $url = Socialite::driver($socialType)
                ->with(['social_type' => $socialType])
                ->stateless()
                ->redirect()
                ->getTargetUrl();
            return response()->json($url);
        }

        return Socialite::driver($socialType)
            ->with(['social_type' => $socialType])
            ->asPopup()
            ->redirect();
    }

    public function socialLoginCallback(Request $request, $type)
    {
        Log::debug("Social login callback for {$type}");

        $user = Socialite::with($type)->user();
        $email = $user->getEmail();
        $registered = $isNewUser = false;

        if ($email) {
            $registered = User::whereEmail($email)->first();
        }

        if (!$registered) {
            if ($email) {
                return response()->redirectToRoute('packages')->with([
                    'status' => trans('login.message_failure_sociallogin', ['email' => $user->getEmail()])
                ]);
            } else {
                return response()->redirectToRoute('login')->with([
                    'status' => trans('login.message_failure_sociallogin_missing_email')
                ]);
            }
            /*            $isNewUser = true;
                        $registered = new User;
                        $registered->email = $user->getEmail();
                        $registered->last_name = $user->getName();
                        $registered->email_verified_at = Carbon::now();
                        $registered->saveOrFail();
                        $registered->package_id = 2;*/
        }

        Auth::login($registered, true);

        if ($isNewUser) {
            return response()->redirectToRoute('podcast.wizard');
        }
        return response()->redirectToRoute('home');
    }

    public function twoFactorAuth(){
        return view('auth.twoFactor');
    }
}
