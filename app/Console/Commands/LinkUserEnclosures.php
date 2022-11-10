<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LinkUserEnclosures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:link-user-enclosures {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Re-)creates a user´s public links for the feed downloads';

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
        $username .= " <" . $user->email . ">";
        $username .= " (" . $user->first_name . ' ' . $user->last_name . ")";

        $feeds = Feed::where('username', '=', $user->username)
            ->pluck('feed_id')
            ->toArray();

        $feedId = $this->choice('Von welchem Feed soll eine Kopie angelegt werden?', $feeds, 0);

        link_user_enclosures($user->username, $feedId);

        return 0;
    }
}
