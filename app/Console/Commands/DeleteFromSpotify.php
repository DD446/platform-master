<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Feed;
use App\Models\User;

class DeleteFromSpotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:delete-from-spotify {spotifyUri : The Spotify URI to remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes a podcast from Spotify';

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
        $spotifyUri = $this->argument('spotifyUri');

        if (!Str::startsWith($spotifyUri, 'spotify:show:')) {
            $spotifyUri = 'spotify:show:' . $spotifyUri;
        }

        if ($this->confirm("Soll der Eintrag für {$spotifyUri} wirklich gelöscht werden?")) {
            \App\Jobs\DeleteFromSpotify::dispatchNow($spotifyUri);
        } else {
            $this->line("Der Löschvorgang wurde abgebrochen.");
            exit();
        }

        $this->line("Überprüfe das Log wegen Fehlermeldungen.");
    }
}
