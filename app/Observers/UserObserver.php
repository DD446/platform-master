<?php
/**
 * User: fabio
 * Date: 06.09.18
 * Time: 13:29
 */

namespace App\Observers;


use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Models\UserPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Jobs\CreateBlogUser;
use App\Jobs\CreateLogfileDir;
use App\Models\User;

class UserObserver
{
    /**
     * Handle to the User "created" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function created(User $user)
    {
        CreateLogfileDir::dispatch($user)->onConnection('redis')->onQueue('superuser');

        CreateBlogUser::dispatch($user, request()->get('password') ?? Str::random())->onConnection('web1');

        $uam = new UserAccountingManager();
        $uam->add($user, Activity::PACKAGE, $user->package_id, 0,
            null, UserPayment::CURRENCY_DEFAULT, now(), $user->date_trialend);

        $when = now()->addMinutes(5);
        Mail::to($user->email)->later($when, new \App\Mail\WelcomeWeekMailable($user));
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        // TODO: Delete feed if soft deleted
        // TODO: Force deleted - Delete all user data like in SGL
    }
}
