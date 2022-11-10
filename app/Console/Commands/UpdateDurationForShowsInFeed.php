<?php

namespace App\Console\Commands;

use App\Events\FeedUpdateEvent;
use Illuminate\Console\Command;
use JamesHeinrich\GetID3\GetID3;
use Khill\Duration\Duration;
use App\Models\Feed;
use App\Models\User;

class UpdateDurationForShowsInFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:update-duration-for-shows-in-feed {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates duration field for all shows of a podcast';

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

throw new \Exception("Not working correctly");

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

        $feed = $this->choice('Für welchen Feed sollen die Durations aktualisiert werden?', $feeds, 0);

        if ($this->confirm('Sollen die Durations für den Feed "' . $feed . '" (Benutzer "' . $username . '") aktualisiert werden?')) {
            $oFeed = Feed::where('username', '=', $user->username)->whereFeedId($feed)->firstOrFail();
            $getID3 = new GetID3();
            $duration = new Duration();
            $shows = $oFeed->shows;

            foreach($shows as &$show) {
                try {
                    if (isset($show->show_media) && !empty($show->show_media)) {
                        $file = get_file($user->username, $show->show_media);
                    }
                } catch (\Exception $e) {
                    $this->error("Did not find media: " . $show->title);
                }

                // Extract duration and save it to data storage
                if ($file) {
                    // HEAVY OPERATION
                    $itunes = $show->itunes ?? [];
                    $itunes['duration'] = get_duration($username, $show->show_media);
                    $this->line("Duration found for show " . $show->title ?? $show->guid . ": " . $itunes['duration']);
                    $show->itunes = $itunes;
                }
            }

            $oFeed->whereUsername($user->username)->whereFeedId($feed)->update(['entries' => $shows]);

            event(new FeedUpdateEvent($oFeed->username, $oFeed->feed_id));
        }
    }
}
