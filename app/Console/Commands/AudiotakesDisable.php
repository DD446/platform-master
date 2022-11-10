<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class AudiotakesDisable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:disable {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switches the ad integration off';

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
        } elseif (is_numeric($userId)) {
            $user = User::findOrFail($userId);
        } else {
            $user = User::where('username', '=', $userId)->firstOrFail();
        }

        $username = $user->username;
        $_user = $user->username;
        $_user .= " <".$user->email.">";
        $_user .= " (".$user->first_name.' '.$user->last_name.")";

        $feeds = Feed::where('username', '=', $username)
            ->pluck('feed_id')
            ->toArray();

        $feedId = $this->choice('Für welchen Feed soll die Monetarisierung deaktiviert werden?', $feeds, 0);

        if ($this->confirm('Soll der Feed "'.$feedId.'" für den Benutzer "'.$_user.'" nicht mehr monetarisiert werden?')) {
            $feed = Feed::whereFeedId($feedId)->whereUsername($username)->first();
            $settings = $feed->settings;
            $settings['audiotakes'] = 0;

            if (!$feed->whereFeedId($feedId)->whereUsername($username)->update(['settings' => $settings])) {
                $this->error("Die Vermarktung konnte nicht deaktiviert werden.");
            }

            $this->line("Die Vermarktung wurde deaktiviert und der Feed wird erneuert.");
            refresh_feed($username, $feedId);
        }

        return 0;
    }
}
