<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

class DeleteBlogUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private /*string*/ $username;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $username)
    {
        if (env('APP_ENV') !== 'production') {
            // Make sure below production url is not called from testing/local, etc.
            return;
        }

        $this->username = Str::lower($username);
        $uri = 'https://podcaster.de/pod-admin/pod-blog.php';

        $params = [
            'action' => 'deleteUser',
            'username' => $username,
        ];

        try {
            $url = $uri;
            $res = Http::asForm()->post($url, $params);

            if (!$res) {
                Log::error("User `{$this->username}`: Delete User {$username}. Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("User `{$this->username}`: Delete User {$username}. Got status code: " . $res->status());
            } else {
                Log::info("User `{$this->username}`: Delete User {$username}. Success. Got status code: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::error("User `{$this->username}`: Delete User {$username}. " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
