<?php

namespace App\Jobs;

use Buzz\Browser;
use Buzz\Client\Curl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Feed;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class DeleteBlogEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $username;
    /**
     * @var Feed
     */
    private $feed;

    private $syncId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $username, Feed $feed, $syncId)
    {
        $this->username = $username;
        $this->feed = $feed;
        $this->syncId = $syncId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $feedId = $this->feed->feed_id;
        $podcast = [
            strtolower($feedId) => [
                'feed_id' => $feedId,
            ]
        ];
        $domain = $this->feed->domain['subdomain'] . '.' . $this->feed->domain['tld'];
        $uri = '/pod-admin/pod-entry.php';
        $params = [
            'action' => 'delete',
            'domain' => $domain,
            'podcast' => $podcast,
            'ID' => $this->syncId,
        ];
        $url = $this->feed->domain['protocol'] . '://' . $domain . $uri;
        $res = Http::asForm()->post($url, $params);

        if ($res->failed()) {
            Log::debug("User: {$this->username}: Deleting blog entry failed for feed `{$feedId}` for show with sync id `{$this->syncId}` with message: " . $res->body());
        }
    }
}
