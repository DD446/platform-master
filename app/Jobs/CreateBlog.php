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

class CreateBlog implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    use InteractsWithQueue {
        fail as interactsFail;
    }

    /**
     * @var User
     */
    private $user;
    private $feedId;
    private $domain;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  string  $feedId
     * @param  array  $domain
     */
    public function __construct(User $user, string $feedId, array $domain)
    {
        $this->user = $user;
        $this->feedId = $feedId;
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = strtolower($this->user->username);

        if (env('APP_ENV') === 'production') {
            $uri = 'https://podcaster.de/pod-admin/pod-blog.php?';
        } else {
            $uri = 'http://wordpress.sattoaster/pod-admin/pod-blog.php?';
        }

        $domain = $this->domain;
        $rss = Feed::where('username', '=', $username)->where('feed_id', '=', $this->feedId)->value('rss');
        $title = $rss['title'];
        $params = http_build_query([
            'action' => 'addBlog',
            'username' => $username,
            'domain' => $domain['subdomain'] . '.' . $domain['tld'],
            'title' => $title,
            'is_custom' => $domain['is_custom'],
        ]);

        $url = $uri . $params;

        Log::debug("User: {$username}. Creating blog `{$title}`...");

        try {
            $res = Http::get($url);

            if (!$res) {
                Log::error("User: {$username}. Creating blog `{$title}`. Error. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User: {$username}. Creating blog `{$title}`. Error. Got status code: " . $res->status());
            } else {
                Log::debug("User: {$username}. Blog `{$title}` created. Success. Got status code: " . $res->status());
                \App\Jobs\CreateBlogFeed::dispatch($this->user, $this->feedId);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("User: {$this->user->username}. Creating blog. Error. Job failed: " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }

}
