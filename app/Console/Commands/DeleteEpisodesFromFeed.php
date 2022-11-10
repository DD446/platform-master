<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class DeleteEpisodesFromFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:delete-episodes-from-feed {userId : The ID of the user} {amount : The number of episodes to be deleted} {--order=oldest : The starting point for deleteion (oldest OR newest)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes x episodes from feed';

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
        $amount = $this->argument('amount');
        $order = $this->option('order');
        $feedId = $this->choice('Aus welchem Feed sollen die ' . $amount . '  Episoden gelöscht werden?', $feeds, 0);
        $feed = Feed::whereFeedId($feedId)->firstOrFail();
        $entries = $feed->entries;
        $countBefore = count($entries);

        if ($order == 'newest') {
            usort($entries, function ($a, $b) {
                return $b['lastUpdate'] <=> $a['lastUpdate'];
            });
        } else if ($order == 'oldest') {
            $entries = Arr::sort($entries, function ($v) {
                return $v['lastUpdate'];
            });
        } else {
            $this->error("Order $order is not supported.");
            return Command::INVALID;
        }

        $entries = array_slice($entries, $amount);
        $countNow = count($entries);

        if ($this->confirm("Episodenanzahl nach `$order` von `$countBefore` auf `$countNow` kürzen?")) {
            if ($feed->whereUsername($feed->username)->whereFeedId($feed->feed_id)->update(['entries' => $entries])) {
                $deleted = $countBefore - $countNow;
                $this->line("Du hast erfolgreich `$deleted` Episoden gelöscht.");
            }
            refresh_feed($feed->username, $feed->feed_id);
        }

        return Command::SUCCESS;
    }
}
