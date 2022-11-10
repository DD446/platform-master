<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feed;
use App\Models\User;

class CreateBlogFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:create-blog-feed {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create custom post type for feed';

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

        $feeds = Feed::where('username', '=', $user->username)
            ->pluck('feed_id')
            ->toArray();

        $feed = $this->choice('Welcher Feed soll synchronisiert werden?', $feeds, 0);

        if ($this->confirm('Soll der Feed "' . $feed . '" für den Benutzer "' . $username . '" synchronisiert werden?')) {
            \App\Jobs\CreateBlogFeed::dispatch($user, $feed);
        }

    }
}
