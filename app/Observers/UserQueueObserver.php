<?php
/**
 * User: fabio
 * Date: 06.09.18
 * Time: 13:29
 */

namespace App\Observers;


use App\Models\UserQueue;

class UserQueueObserver
{
    /**
     * Handle to the User "created" event.
     *
     * @param  \App\Models\UserQueue  $userQueue
     * @return void
     */
    public function created(UserQueue $userQueue)
    {
        $userQueue->sendActivationNotification();
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param UserQueue $userQueue
     * @return void
     */
    public function deleted(UserQueue $userQueue)
    {
        //
    }
}
