<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshUserFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:refresh-user-feeds {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a fresh copy of all the podcast feeds of an user.';

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
        $userId = $this->argument('userId');

        if (strpos($userId, '@') !== false) {
            $user = User::where('email', '=', $userId)->firstOrFail();
        } elseif(is_numeric($userId)) {
            $user = User::findOrFail($userId);
        } else {
            $user = User::where('username', '=', $userId)->firstOrFail();
        }

        $username = $user->username;

        $feeds = Feed::whereUsername($username)->get();

        foreach ($feeds as $feed) {
            try {
                $this->line("User `{$feed->username}`: Renewing feed with id `{$feed->feed_id}`");
                refresh_feed($feed->username, $feed->feed_id);
            } catch (\Exception $e) {
                $this->error("User `{$feed->username}`: Failed to refresh feed with id `{$feed->feed_id}`.");
            }
        }

        return Command::SUCCESS;
    }
}
