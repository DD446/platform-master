<?php

namespace App\Console\Commands;

use App\Classes\MediaManager;
use App\Models\Feed;
use App\Models\Media;
use App\Models\Show;
use App\Models\User;
use Illuminate\Console\Command;

class CleanUpTrialAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:cleanup-trials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes unused accounts after end of trial period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now()->subMonths(2);
        $users = User::whereDate('date_trialend', '<', $now)->where('funds', '<', 0)->get();

        foreach ($users as $user) {
            // Check if user created a podcast feed
            $cFeed = Feed::whereUsername($user->username)->count();

            if ($cFeed === 0) {
                // Some people do not use a feed
                $mm = new MediaManager($user);
                if ($mm->count() < 5) {
                    $user->delete();
                }
            } elseif ($cFeed === 1) {
                $feed = Feed::whereUsername($user->username)->first();
                $cShows = count($feed->shows);
                if ($cShows == 0) {
                    $user->delete();
                } elseif ($cShows == 1) {
                    $show = $feed->shows->first();
                    if ($show->is_public == Show::PUBLISH_DRAFT) {
                        $user->delete();
                    }
                }
            }
        }

        return 0;
    }
}
