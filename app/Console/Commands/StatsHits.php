<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Feed;
use App\Models\User;

class StatsHits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:stats-hits {days=30 : Number of days to calculate} {--A|adoptin : Calculate only for users with ad opt-in} {--C|cleanup : Remove feeds from non-existing users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates hits for all active feeds';

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
        $days = $this->argument('days');
        $cleanup = $this->option('cleanup');
        $adOptIn = $this->option('adoptin');
        $cFeeds = 0;
        $dFeeds = 0;
        $iFeeds = 0;
        $userNotFound = [];
        $cHits = 0;

        foreach (Feed::select(['username', 'feed_id', 'settings.ads'])->cursor() as $feed) {
            ++$cFeeds;
            $username = $feed->username;
            $feedId = $feed->feed_id;
            $user = User::whereUsername($username)->select(['usr_id'])->first();

            if (!$user) {
                if (!in_array($username, $userNotFound)) {
                    array_push($userNotFound, $username);
                }

                if ($cleanup) {
                    $this->line("Cleaning up user: " . $username);

                    if (Feed::whereUsername($username)->whereFeedId($feedId)->delete()) {
                        $this->line("Feed `".$feedId."` deleted.");
                        ++$dFeeds;
                    }

                    DB::table('requests_'.$username)->delete();
                    DB::table('logs_'.$username)->delete();
                    DB::table('downloads_'.$username)->delete();
                }
            } else {
                if ($adOptIn) {
                    if (!isset($feed->settings['ads']) || $feed->settings['ads'] != 1) {
                        continue;
                    }
                }

                $hits = DB::connection('mongodb')
                    ->table('requests_' . $username)
                    ->whereFeedId($feedId)
                    ->whereType('day')
                    ->whereIn('user_agent_type', ['apps', 'browsers', 'desktop'])
                    ->where('user_agent', 'exists', false)
                    ->where('created', '>', new \MongoDB\BSON\UTCDateTime(now()->subDays($days)->getTimestamp()*1000))
                    /*->select(['hits'])*/
                    ->sum('hits');

                if ($hits) {
                    $this->line("User `{$username}`: Feed: `{$feedId}` has `{$hits}` hits in the last `{$days}` days.");
                    $cHits += $hits;
                    ++$iFeeds;
                }
            }
        }

        $this->line("Feeds found: `$cFeeds`");
        $this->line("Users not found: " . count($userNotFound));
        $this->line("Deleted feeds: `$dFeeds`");
        $this->line("`{$cHits}` hits in the last `{$days}` days from `{$iFeeds}` inspected feeds.");

        return 0;
    }
}
