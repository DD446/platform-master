<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feed;
use App\Models\User;

class DeleteFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:delete-feed {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes an user´s feed';

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

        $feed = $this->choice('Welcher Feed soll gelöscht werden?', $feeds, 0);

        if ($this->confirm('Soll der Feed "' . $feed . '" für den Benutzer "' . $username . '" wirklich gelöscht werden?')) {
            $_feed = Feed::where('username', '=', $user->username)->where('feed_id', '=', $feed)->firstOrFail();
            if (!$_feed->delete()) {
                $this->error("Der Feed wurde nicht gelöscht.");
                exit(-1);
            }
        } else {
            $this->line("Der Löschvorgang wurde abgebrochen.");
            exit();
        }

        $this->line("Der Feed {$feed} wurde erfolgreich gelöscht.");
    }
}
