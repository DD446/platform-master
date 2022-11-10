<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use App\Rules\PasswordIsCorrect;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        \SEO::setTitle(trans('login.page_title_forgot_password'));
        \SEO::metatags()->addMeta(['robots' => 'noindex']);

        return view('auth.passwords.email', ['hideNav' => true]);
    }

    public function index()
    {
        return view('auth.password.index', ['user' => auth()->user()]);
    }

    public function update(Request $request, User $user)
    {
        $msg = ['success' => trans('auth.success_password_update')];

        $this->validate($request, [
            'password_existing' => ['required', new PasswordIsCorrect],
            'password' => 'required|confirmed|min:6|different:password_existing',
        ], [], [
            'password' => trans('auth.new_password'),
            'password_existing' => trans('auth.old_password'),
        ]);

        $user->password = Hash::make($request->password);

        if (!$user->update()) {
            $msg = ['error' => trans('auth.failure_password_update')];
        }

        return redirect()->back()->with($msg);
    }
}
