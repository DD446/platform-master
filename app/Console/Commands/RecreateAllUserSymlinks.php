<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\Media;
use App\Models\User;
use Illuminate\Console\Command;

class RecreateAllUserSymlinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:recreate-user-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes all links for all users';

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
        $cursor = User::whereIn('role_id', [User::ROLE_USER, User::ROLE_EDITOR])->cursor();

        foreach($cursor as $user) {
            $this->line('Processing: ' . $user->username);

            $this->call('podcaster:link-files', ['userId' => $user->id]);
            $this->call('podcaster:link-user-enclosures', ['userId' => $user->id]);

/*            $feeds = Feed::whereUsername($user->username)->get();

            foreach ($feeds as $feed) {
                $this->line('User ' . $user->username . ": Feed " . $feed->feed_id);
                foreach ($feed->shows as $show) {
                    if (isset($show->guid) && !empty($show->guid)) {
                        $this->line('Show: ' . $show->guid);
                        link_show_for_feed($user->username, $feed, $show->guid);
                    }
                }
            }*/
        }

        return 0;
    }
}
