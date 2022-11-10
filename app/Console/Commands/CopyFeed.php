<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Feed;
use App\Models\User;

class CopyFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:copy-feed {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a copy of a feed with a new url and feed id (optional)';

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

        $feedId = $this->choice('Von welchem Feed soll eine Kopie angelegt werden?', $feeds, 0);
        $feed = Feed::whereUsername($user->username)->whereFeedId($feedId)->firstOrFail();
        $newFeedId = $this->ask("Wie soll die neue Feed-ID lauten?");

        if ($this->confirm('Soll eine Kopie des Feeds "' . $feedId . '" für den Benutzer "' . $username . '" mit der Feed-ID `' . $newFeedId. '` erstellt werden?')) {
            $newFeed = $feed->replicate();
            unset($newFeed->_id);
            $newFeed->feed_id = $newFeedId;
            if (!$newFeed->save()) {
                exit("Es konnte keine Kopie des Feeds erstellt werden.");
            }
            $this->line("Du hast die Kopie des Feeds erfolgreich erstellt!");
        };
    }
}
