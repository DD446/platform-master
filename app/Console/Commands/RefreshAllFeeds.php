<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshAllFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:refresh-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renews all customer feeds';

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
        if ($this->confirm('Sollen alle Feeds aktualisiert werden?')) {
            $cursor = User::whereIn('role_id', [User::ROLE_USER, User::ROLE_EDITOR])->cursor();

            foreach($cursor as $user) {
                $this->line('Processing: ' . $user->username);
                $feeds = Feed::whereUsername($user->username)->get();

                foreach ($feeds as $feed) {
                    try {
                        $this->line("User `{$feed->username}`: Renewing feed with id `{$feed->feed_id}`");
                        refresh_feed($feed->username, $feed->feed_id);
                    } catch (\Exception $e) {
                        $this->error("User `{$feed->username}`: Failed to refresh feed with id `{$feed->feed_id}`.");
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
