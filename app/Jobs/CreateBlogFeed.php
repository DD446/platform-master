<?php

namespace App\Jobs;

use Buzz\Browser;
use Buzz\Client\Curl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Feed;
use App\Models\User;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class CreateBlogFeed implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    use InteractsWithQueue {
        fail as interactsFail;
    }

    private $feed;
    private $domain;
    private $podcast;
    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $feed)
    {
        $this->user = $user;
        $this->feed = $feed;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = $this->user->username;
        $feed = $this->feed;
        $lfeed = mb_strtolower($feed);
        $oFeed = Feed::where('username', '=', $username)
            ->where('feed_id', '=', $feed)
            ->firstOrFail();

        $podcast = [
            strtolower($feed) => [
                'feed_id'           => $feed,
                'name'              => $oFeed->rss['title'],
                'description'       => $oFeed->rss['description'],
            ]
        ];

        $domain = $oFeed->domain['subdomain'] . '.' . $oFeed->domain['tld'];

        if (env('APP_ENV') === 'production') {
            $uri = '/pod-admin/pod-feed.php';
        } else {
            $uri = '/pod-admin/pod-feed.php';
        }

        $params = [
            'action' => 'add',
            'domain' => $domain,
            'podcast' => $podcast,
        ];

        try {
            $url = $oFeed->domain['hostname'] . $uri;
            $res = Http::asForm()->post($url, $params);

            Log::debug("User: {$username}. Creating blog feed `{$podcast[$lfeed]['name']}`...");

            if (!$res) {
                Log::error("User: {$username}. Creating blog feed `{$podcast[$lfeed]['name']}`. Error. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User: {$username}. Creating blog feed `{$podcast[$lfeed]['name']}`. Error. Got status code: " . $res->status());
            } else {
                Log::debug("User: {$username}. Blog feed `{$podcast[$lfeed]['name']}` created. Success. Got status code: " . $res->status());
                if ($oFeed->usesWebsite()) {
                    \App\Jobs\SyncShowsToBlog::dispatch($oFeed);
                }
            }
        } catch (\Exception $e) {
            Log::error("User: {$username}. Creating blog feed `{$podcast[$lfeed]['name']}`. Error: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("User: {$this->user->username}. Creating blog feed. Job failed: " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }

}
