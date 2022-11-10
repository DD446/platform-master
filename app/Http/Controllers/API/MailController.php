<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MailController extends Controller
{
    /**
     * @param $userId
     * @param $hash
     * @return \Illuminate\Http\RedirectResponse|void
     * @hideFromAPIDocumentation
     */
    public function welcomeUnsubscribe($userId, $hash)
    {
        $user = User::findOrFail($userId);

        $_hash = sha1($user->id . $user->created_at);

        if ($_hash !== $hash && $user->welcome_email_state > -1) {
            abort('404', trans('mail.error_message_welcome_week_hash_not_valid'));
        }

        $user->welcome_email_state = -1;

        if ($user->save()) {
            $msg = ['status' => trans('mail.success_message_welcome_week_mail_unsubscribed')];
            return redirect()->route('home')->with($msg);
        }
    }
}
