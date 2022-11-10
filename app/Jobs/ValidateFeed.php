<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Classes\FeedValidator;
use App\Models\User;

class ValidateFeed implements ShouldQueue
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
     * @var string
     */
    private $feed;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, string $feed, string $type, string $uuid = null)
    {
        //
        $this->user = $user;
        $this->feed = $feed;
        $this->type = $type;
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $prefix = "App\Classes\FeedValidator\\";
        $class = $prefix . $this->type;
        /** @var FeedValidator $class */
        $o = new $class($this->user->username, $this->feed, $this->uuid);
        $o->run();
    }

    public function fail(\Exception $exception = null)
    {
        $this->interactsFail($exception);

        Log::error("Job failed: " . $exception instanceof \Exception ? $exception->getTraceAsString() : null);
    }
}
