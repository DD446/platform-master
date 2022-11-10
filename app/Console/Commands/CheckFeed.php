<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\FeedValidator;
use App\Models\Feed;
use App\Models\User;

class CheckFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:check-feed {userId : The ID of the user} {feedId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs feed validation';

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
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $feedId = $this->argument('feedId');

        if (strpos($userId, '@') !== false) {
            $user = User::where('email', '=', $userId)->firstOrFail();
        } elseif(is_numeric($userId)) {
            $user = User::findOrFail($userId);
        } else {
            $user = User::where('username', '=', $userId)->firstOrFail();
        }

        $username = $user->username;
        $username .= " <" . $user->email . ">";
        $username .= " (" . $user->first_name . ' ' . $user->last_name . ")";

        if (!$feedId) {
            $feeds = Feed::whereUsername($user->username)->pluck('feed_id')->toArray();
            $feedId = $this->choice("Which feed to you want to validate?", $feeds, 0);
        }

        foreach (['Channel', 'Logo', 'Show', 'Enclosure'] as $type) {
            $class = "App\Classes\FeedValidator\\".$type;
            /** @var FeedValidator $class */
            $o = new $class($user->username, $feedId);
            $res = $o->run();
        }

        foreach ($res as $keys => $a) {
            $this->line($keys);
            foreach($a as $m) {
                $this->line($m);
            }
        }
    }
}
