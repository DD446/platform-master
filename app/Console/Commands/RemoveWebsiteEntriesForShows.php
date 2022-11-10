<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RemoveWebsiteEntriesForShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:remove-website-entries {userId : The ID of the user} {--F|feedId= : The ID of the feed} {--M|match= : Text to match}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes url entries for shows by pattern';

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

        $feeds = Feed::whereUsername($username)
            ->pluck('feed_id')
            ->toArray();

        $feedId = $this->option('feedId');

        if (!$this->option('feedId')) {
            $feedId = $this->choice('Welcher Feed soll gesäubert werden?', $feeds, 0);
        }

        if (!in_array($feedId, $feeds)) {
            $this->error("Die FeedID `{$feedId}` ist nicht bekannt.");
            exit(1);
        }

        $match = $this->option('match');

        if (!$this->option('feedId')) {
            $match = $this->ask('Welcher Text im Link soll gefunden werden?');
        }

        $feed = Feed::whereUsername($username)->where('feed_id', '=', $feedId)->first();
        $hasChanges = false;

        if ($feed) {
            $entries = $feed->entries;

            if ($entries) {
                foreach ($entries as &$entry) {
                    if (isset($entry['link'])) {
                        if (Str::contains($entry['link'], $match)) {
                            $this->line("Match: ".$entry['link']);
                            unset($entry['link']);
                            $hasChanges = true;
                        }
                    }
                }
            }

            if ($hasChanges) {
                $feed->whereUsername($feed->username)
                    ->whereFeedId($feed->feed_id)
                    ->update([
                        'entries' => array_values($entries)
                    ]);
            }
        }

        return Command::SUCCESS;
    }
}
