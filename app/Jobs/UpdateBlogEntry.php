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

class UpdateBlogEntry implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    use InteractsWithQueue {
        fail as interactsFail;
    }

    private $feedId;
    private $domain;
    private $podcast;
    /**
     * @var string
     */
    private $username;

    /**
     * Create a new job instance.
     *
     * @param  string  $username
     * @param  string  $feedId
     */
    public function __construct(string $username, string $feedId)
    {
        $this->username = $username;
        $this->feedId = $feedId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = $this->username;
        $feed = $this->feedId;
        $lfeed = mb_strtolower($feed);
        $oFeed = Feed::whereUsername($username)
            ->whereFeedId($feed)
            ->firstOrFail();

        $podcast = [
            $lfeed => [
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
            'action' => 'update',
            'domain' => $domain,
            'podcast' => $podcast,
        ];

        try {
            $url = $oFeed->domain['hostname'] . $uri;
            $res = Http::asForm()->post($url, $params);

            Log::debug("User: {$username}. Updating blog feed `{$podcast[$lfeed]['name']}` for domain `{$domain}`...");

            if (!$res) {
                Log::error("User: {$username}. Updating blog feed `{$podcast[$lfeed]['name']}` for domain `{$domain}`. Error. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User: {$username}. Updating blog feed `{$podcast[$lfeed]['name']}` for domain `{$domain}`. Error. Got status code: " . $res->status());
            } else {
                Log::debug("User: {$username}. Blog feed `{$podcast[$lfeed]['name']}` for domain `{$domain}` updated. Success. Got status code: " . $res->status());
                \App\Jobs\SyncShowsToBlog::dispatch($oFeed);
            }
        } catch (\Exception $e) {
            Log::error("User: {$username}. Updating blog feed `{$podcast[$lfeed]['name']}` for domain `{$domain}`. Error: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("User: {$this->username}. Updating blog entry. Job failed: " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }

}
