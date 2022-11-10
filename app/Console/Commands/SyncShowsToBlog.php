<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\FeedWriterLegacy;
use App\Models\Feed;
use App\Models\User;

class SyncShowsToBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:sync-shows {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all shows to blog';

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

        $feed = $this->choice('Für welchen Feed sollen die Episoden synchronisiert werden?', $feeds, 0);

        $feed = Feed::where('username', '=', $user->username)->where('feed_id', '=', $feed)->firstOrFail();

        if (count($feed->entries) < 1) {
            $this->error("Der Podcast `"  . $feed->rss['title'] . "` enthält keine Episoden. Nichts zu syncen.");
            exit(-1);
        }

        $this->line("Synce die Episoden von Podcast `"  . $feed->rss['title'] . "`...");

/*        $feedWriter = new FeedWriterLegacy();
        $res = $feedWriter->syncShows($feed);

        $this->line("Synchronisieren der Episoden ergab folgende Rückmeldung: ");
        $this->line($res);
        $this->line("Die Episoden von Podcast `"  . $feed->rss['title'] . "` sind gesynct.");*/

        \App\Jobs\SyncShowsToBlog::dispatchSync($feed);
    }
}
