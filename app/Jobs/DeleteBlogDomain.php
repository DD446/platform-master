<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteBlogDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;
    private array $origDomain;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  array  $origDomain
     */
    public function __construct(User $user, array $origDomain)
    {
        //
        $this->user = $user;
        $this->origDomain = $origDomain;
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

        $origDomain = $this->origDomain;
        $params = http_build_query([
            'action' => 'deleteBlogDomain',
            'username' => $username,
            'orig_domain' => $origDomain['subdomain'] . '.' . $origDomain['tld'],
        ]);

        $url = $uri . $params;

        try {
            $res = Http::get($url);

            if (!$res) {
                Log::error("User: {$username}. Deleteing blog domain `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "` failed. Error. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User: {$username}. Deleteing blog domain `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "` failed. Error. Got status code: " . $res->status());
            } else {
                Log::debug("User: {$username}. Deleted blog domain `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "`. Success. Got status code: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }
}
