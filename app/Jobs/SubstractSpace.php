<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Space;
use App\Models\User;

class SubstractSpace implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;
    private array $file;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  array  $file
     */
    public function __construct(User $user, array $file)
    {
        $this->user = $user;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spaces = Space::available()
            ->whereUserId($this->user->id)
            ->orderBy('type')
            ->where('space_available', '>', 0)
            ->get();
        $size = $this->file['byte'];

        // Substract from regular storage first
        foreach($spaces as $space) {
            // Get available space
            if ($space->space_available >= $size) {
                // There is enough space here to substract everything
                $space->decrement('space_available', $size);
                // Create log
                \App\Jobs\LogUserUpload::dispatch($this->user, $this->file, $space->id, $size);
                $size = 0;
                break;
            } else {
                // Substract as much space as there is
                $availableSpace = $space->space_available;
                // This should end in 0 space_available
                $space->decrement('space_available', $availableSpace);
                // Create log
                \App\Jobs\LogUserUpload::dispatch($this->user, $this->file, $space->id, $availableSpace);
                // There is more we need to substract
                $size = $size - $availableSpace;

                if ($size <= 0) {
                    break;
                }
            }
        }

        // If user used more space than was available make default space negative
        if ($size > 0) {
            // This should never fail!
            $space = Space::whereUserId($this->user->id)
                ->whereType(Space::TYPE_REGULAR)
                ->firstOrFail();
            if ($space) {
                $space->decrement('space_available', $size);// Create log
                \App\Jobs\LogUserUpload::dispatch($this->user, $this->file, $space->id, $size);
            }
        }
    }
}
