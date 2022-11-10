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

class AddBlogDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;
    private array $newDomain;
    private array $origDomain;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  array  $newDomain
     * @param  array  $origDomain
     */
    public function __construct(User $user, array $newDomain, array $origDomain)
    {
        //
        $this->user = $user;
        $this->newDomain = $newDomain;
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

        $domain = $this->newDomain;
        $origDomain = $this->origDomain;
        $params = http_build_query([
            'action' => 'addBlogDomain',
            'username' => $username,
            'domain' => $domain['subdomain'] . '.' . $domain['tld'],
            'orig_domain' => $origDomain['subdomain'] . '.' . $origDomain['tld'],
        ]);

        $url = $uri . $params;

        try {
            $res = Http::get($url);

            if (!$res) {
                Log::error("User: {$username}. Adding blog domain for `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "` as " . $domain['subdomain'] . '.' . $domain['tld'] . "` failed. Error. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User: {$username}. Adding blog domain for `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "` as " . $domain['subdomain'] . '.' . $domain['tld'] . "` failed. Error. Got status code: " . $res->status());
            } else {
                Log::debug("User: {$username}. Added blog domain for `" . $origDomain['subdomain'] . '.' . $origDomain['tld'] . "` as " . $domain['subdomain'] . '.' . $domain['tld'] . "`. Success. Got status code: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }
}
