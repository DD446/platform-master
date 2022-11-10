<?php

namespace App\Listeners;

use App\Events\FeedUpdateEvent;
use App\Events\ShowAddEvent;
use App\Events\UpdateShowGuid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class ShowGuidUpdateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdateShowGuid  $event
     * @return void
     */
    public function handle(UpdateShowGuid $event)
    {
        $user = User::whereUsername($event->username)->first();

        if ($user) {
            $usage = $user->usageFile($event->fileId);

            if ($usage->count() > 0) {
                $oFeed = $usage->shift();

                foreach ($oFeed->shows as &$show) {
                    if ($show['show_media'] == $event->fileId) {
                        $show['guid'] = get_guid('pod-');
                        if ($show->save()) {
                            event(new FeedUpdateEvent($event->username, $oFeed->feed_id));
                            event(new ShowAddEvent($event->username, $oFeed->feed_id, $show['guid']));
;                        }
                    }
                }
            }
        }
    }
}
