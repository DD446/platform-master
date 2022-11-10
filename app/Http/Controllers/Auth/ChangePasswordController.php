<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use App\Rules\PasswordIsCorrect;
use Illuminate\Support\Facades\Log;

class ChangePasswordController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function index()
    {
        return view('auth.passwords.index', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $msg = ['success' => trans('auth.success_password_update')];

        $validated = $this->validate($request, [
            'password_existing' => ['required', new PasswordIsCorrect],
            'password' => 'required|confirmed|min:6|different:password_existing',
        ], [], [
            'password' => trans('auth.new_password'),
            'password_existing' => trans('auth.old_password'),
        ]);

        //$user->passwd = Hash::make($request->password);
        $user->passwd = md5($validated['password']);
        $user->password = password_hash($validated['password'], PASSWORD_BCRYPT);

        if (!$user->save()) {
            $msg = ['error' => trans('auth.failure_password_update')];
        } else {
            try {
                Auth::logoutOtherDevices($validated['password']);
            } catch (\Exception $e) {
                Log::error("User {$user->username}: Logout on other devices failed: " . $e->getMessage());
            }
        }

        return redirect()->back()->with($msg);
    }
}
