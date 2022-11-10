<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserUpload;

class LogUserUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;
    private int $spaceId;
    private int $spaceUsed;
    private array $file;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  array  $file
     * @param  int  $spaceId
     * @param  int  $spaceUsed
     */
    public function __construct(User $user, array $file, int $spaceId, int $spaceUsed)
    {
        $this->user = $user;
        $this->spaceId = $spaceId;
        $this->spaceUsed = $spaceUsed;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $uu = new UserUpload();
        $uu->user_id = $this->user->user_id;
        $uu->file_id = $this->file['id'];
        $uu->file_size = $this->file['byte'];
        $uu->file_name = $this->file['name'];
        $uu->space_id = $this->spaceId;
        $uu->space_used = $this->spaceUsed;
        $uu->saveOrFail();
    }
}
