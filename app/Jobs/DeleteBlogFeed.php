<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Feed;
use App\Models\User;

class DeleteBlogFeed implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    use InteractsWithQueue {
        fail as interactsFail;
    }

    private $feedId;
    private $domain;
    private $podcast;

    /**
     * @var User
     */
    private $username;

    /**
     * Create a new job instance.
     *
     * @param  string  $username
     * @param  string  $feedId
     * @param  array  $domain
     */
    public function __construct(string $username, string $feedId, array $domain = null)
    {
        $this->username = strtolower($username);
        $this->feedId = $feedId;

        if (is_null($domain)) {
            $feed = Feed::where('username', '=', $username)->where('feed_id', '=', $feedId)->firstOrFail();
            $domain = $feed->domain;
        }
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $feed = $this->feedId;

        $podcast = [
            strtolower($feed) => [
                'feed_id' => $feed,
            ]
        ];

        $domain = $this->domain['subdomain'] . '.' . $this->domain['tld'];

        if (env('APP_ENV') === 'production') {
            $uri = '/pod-admin/pod-feed.php';
        } else {
            $uri = '/pod-admin/pod-feed.php';
        }

        $params = [
            'action' => 'delete',
            'domain' => $domain,
            'podcast' => $podcast,
        ];

        try {
            $url = $this->domain['hostname'] . $uri;
            $res = Http::asForm()->post($url, $params);

            if (!$res) {
                Log::error("User `{$this->username}`: Delete Blog Feed {$feed}. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User `{$this->username}`: Delete Blog Feed {$feed}. Got status code: " . $res->status());
            } else {
                Log::info("User `{$this->username}`: Delete Blog Feed {$feed}. Success. Got status code: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::error("User `{$this->username}`: Delete Blog Feed {$feed}. " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("User `{$this->username}`: Job failed: Delete Blog Feed {$this->feedId}. " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }

}
