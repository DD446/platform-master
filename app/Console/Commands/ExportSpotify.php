<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feed;

class ExportSpotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:export-spotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the podcasts that signed up for Spotify';

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
        $c = Feed::whereNotNull('settings.spotify_uri')->where('settings.spotify', '=', "0")->update(['settings.spotify' => "1"]);
        $this ->line("Updated {$c} entries with 0");
        $c = Feed::whereNotNull('settings.spotify_uri')->where('settings.spotify', 'exists', false)->update(['settings.spotify' => "1"]);
        $this->line("Updated {$c} entries with non");

        $feeds = Feed::select('feed_id', 'domain', 'settings.spotify_uri')
            ->where('settings.spotify', '=', '1')
            ->get();

        if (!$feeds) {
            return false;
        }

        $this->line("Found " . $feeds->count() . " entries");
        $file = storage_path('app/public/spotify.csv');
        $fp = fopen($file, 'w');

        foreach ($feeds as $feed) {
            $link = get_feed_uri($feed->feed_id, $feed->domain);
            $fields = [
                $link
            ];
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }
}
