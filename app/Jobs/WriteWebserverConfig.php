<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Classes\DomainManager;
use App\Models\User;

class WriteWebserverConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $feedId;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  string  $feedId
     * @param  array  $domain
     */
    public function __construct(User $user, string $feedId)
    {
        $this->user = $user;
        $this->feedId = $feedId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dm = new DomainManager();
        $dm->writeConfig($this->user->username, $this->feedId);
    }
}
