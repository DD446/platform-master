<?php

namespace App\Console\Commands;

use App\Classes\Domain;
use App\Http\Controllers\API\FeedController;
use App\Http\Requests\StoreFeedRequest;
use Illuminate\Console\Command;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;

class ImportFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:import-feed {userId : The ID of the user} {--F|feedId= : The ID of the feed} {--U|url= : The URL to import from} {--S|subdomain= : Subdomain of feed} {--D|domain= : Domain of feed} {--T|tld= : TLD of domain of feed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an existing podcast feed as a new podcast for an user';

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
            $feedId = $this->ask('Wie soll die Feed-ID lauten? Folgende IDs sind bereits vergeben: '.implode(', ',
                    $feeds));
        }

        if (in_array($feedId, $feeds)) {
            $this->error("Die FeedID `{$feedId}` ist bereits vergeben und kann nicht für den Import verwendet werden.");
            exit(1);
        }

        $url = $this->option('url');

        if (!$url) {
            $url = $this->ask('Wie ist die URL, die importiert werden soll?');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->error("URL muss eine gültige Internetadresse sein.");
            exit(1);
        }

        $this->line("Importiere Podcast jetzt...");

        //ImportFeedFromUrl::dispatchSync($user, $url, $feedId);
        //$fil = new FeedImporterLegacy();
        //$fil->import($feedId, $username, $url);

        $domain = (new Domain)->getDomainDefaults($username);

        if ($this->option('subdomain') && $this->option('domain') && $this->option('tld')) {
            $domain['subdomain'] = $this->option('subdomain');
            $domain['domain'] = $this->option('domain');
            $domain['tld'] = $this->option('tld');
        }

        $attributes = ['feed_id' => $feedId, 'feed_url' => $url, 'username' => $username, 'domain' => $domain];
        request()->replace($attributes);
        $request = app('App\Http\Requests\StoreFeedRequest');
        $fc = new FeedController();
        $this->info($fc->store($request));

        $this->line("Import abgeschlossen.");

        return 0;
    }
}
