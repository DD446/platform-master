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
use App\Models\User;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class CreateBlogUser implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    use InteractsWithQueue {
        fail as interactsFail;
    }

    /**
     * @var User
     */
    private $user;

    /**
     * @var String
     */
    private $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = strtolower($this->user->username);
        $password = $this->password;

        if (env('APP_ENV') === 'production') {
            $uri = 'https://podcaster.de/pod-admin/pod-blog.php?';
        } else {
            $uri = 'http://wordpress.sattoaster/pod-admin/pod-blog.php?';
        }

        $params = http_build_query([
            'action' => 'addBlogUser',
            'username' => $username,
            'email' => $this->user->email,
            'password' => $password,
        ]);

        $url = $uri . $params;

        try {
            $res = Http::get($url);

            if (!$res) {
                Log::error("Username {$username}: Create Blog User. Error: Did not get a response ");
            } elseif($res->status() != 200) {
                Log::critical("Username {$username}: Create Blog User. Got status code: " . $res->status());
            } else {
                Log::info("Username {$username}: Create Blog User. Success. Got status code: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("Job failed: " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }

}
