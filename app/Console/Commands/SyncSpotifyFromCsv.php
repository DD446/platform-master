<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Feed;

class SyncSpotifyFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:sync-spotify-from-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads a list of uris and syncs them with the db';

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
        $file = storage_path('spotify_uris.csv');

        if (!File::isFile($file)) {
            $this->error("File {$file} not found.");
            exit(-1);
        }

        $updates = $lines = $hits = 0;

        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $lines++;
                list($suri, $uri) = $data;
                $p = parse_url($uri);
                $hostname = $p['scheme'] . '://' . $p['host'];
                $feedId = Str::replaceLast('.rss', '', ltrim($p['path'], '/'));
                $feed = Feed::select('settings')->where('domain.hostname', '=', $hostname)->where('feed_id', '=', $feedId)->first();

                if ($feed) {
                    $hits++;
                    if (!isset($feed['settings']['spotify_uri']) || $feed['settings']['spotify_uri'] != $suri) {
                        $_feed = [];
                        $_feed['settings']['spotify_uri'] = $suri;
                        $_feed['settings']['spotify_updated'] = Carbon::now();

                        if (DB::connection('mongodb')->collection('feeds')->where('domain.hostname', '=', $hostname)->where('feed_id', '=', $feedId)->update($_feed) == 1) {
                            $updates++;
                        }
                    }
                }
            }
            fclose($handle);
        }

        $this->line("Found {$lines} lines. Got {$hits} entries. Updated {$updates} entries.");
    }
}
