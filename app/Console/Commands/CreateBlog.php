<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feed;
use App\Models\User;

class CreateBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:create-blog {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a WordPress blog';

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
        $username .= " <" . $user->email . ">";
        $username .= " (" . $user->first_name . ' ' . $user->last_name . ")";

        $feeds = Feed::where('username', '=', $user->username)
            ->pluck('feed_id')
            ->toArray();

        $feed = $this->choice('Für welchen Feed soll ein Blog angelegt werden?', $feeds, 0);

        $domains = Feed::where('username', '=', $user->username)
            ->where('domain.website_type', '=', 'wordpress')
            ->select('domain')
            ->get();

        $blogs = [];

        foreach ($domains as $domain) {
            array_push($blogs, $domain->domain['subdomain'] . '.' . $domain->domain['tld']);
        }

        if (count($blogs) < 1) {
            array_push($blogs, $user->username . '.' . config('app.name'));
        }

        $domain = $this->anticipate('Für welche Domain soll das Blog angelegt werden?', $blogs, $blogs[0]);

        //$isCustom = $this->choice('Handelt es sich um eine "custom" Domain?', ['Nein', 'Ja'], 0);
        $aDomain = $domains[array_search($domain, $blogs)]->domain;

        if ($this->confirm('Soll ein Blog unter der Domain "' . $domain . '" für den Feed "' . $feed . '" für den Benutzer "' . $username . '" angelegt werden?')) {
            \App\Jobs\CreateBlog::dispatchNow($user, $feed, $aDomain);
        }
    }
}
