<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class MergeFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:merge-feeds {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $feed = $this->choice('Aus welchem Feed sollen die Episoden genommen werden?', $feeds, 0);

        if (($key = array_search($feed, $feeds)) !== false) {
            unset($feeds[$key]);
        }

        $goalFeed = $this->choice('In welchen Feed sollen die Episoden gemergt werden?', $feeds, 0);

        if ($feed == $goalFeed) {
            $this->error("Cannot merge to the same feed.");
            return Command::INVALID;
        }

        $feedFrom = Feed::where('username', '=', $user->username)->where('feed_id', '=', $feed)->firstOrFail();
        $feedTo = Feed::where('username', '=', $user->username)->where('feed_id', '=', $goalFeed)->firstOrFail();

        $entriesFrom = $feedFrom->entries;
        $entriesTo = $feedTo->entries;
        $this->line("Merging " . count($entriesFrom) . " entries with " . count($entriesTo) . " entries.");
        $expected = count($entriesFrom) + count($entriesTo);
        $shows = collect($feedTo->entries);

        foreach ($entriesFrom as $entry) {
            $show = $shows->firstWhere('guid', $entry['guid']);

            if ($show) {
                // Set a new GUID if given one is already/also used in merged feed
                $entry['guid'] = get_guid('pod_');
                $this->line('Setting new guid.');
            }
            $this->line('Adding entry: ' . $entry['title']);
            array_push($entriesTo, $entry);
        }
        $this->line("Merged shows have " . count($entriesTo) . " entries. Expected: " . $expected);

        if (count($entriesTo) != $expected) {
            $this->error('Count of merged shows differs from expected. Aborting.');
            return Command::FAILURE;
        }

        $this->line("Saving merged shows.");

        if (!$feedTo->where('username', '=', $user->username)->where('feed_id', '=', $goalFeed)->update(['entries' => $entriesTo])) {
            $this->error("Failed saving merged shows.");
            return Command::FAILURE;
        }

        $this->line("Saved merged shows.");

        return Command::SUCCESS;
    }
}
