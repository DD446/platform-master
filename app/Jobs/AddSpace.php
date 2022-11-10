<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Space;
use App\Models\User;
use App\Models\UserUpload;
use Illuminate\Support\Facades\Log;

class AddSpace implements ShouldQueue
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
        DB::beginTransaction();

        try {
            $size = $this->file['byte'];// Re-add space
            $upload = UserUpload::whereUserId($this->user->id)->whereFileId($this->file['id'])->firstOrFail();
            // Check if upload was in this accounting period
            $at = get_user_accounting_times($this->user->id);

            if ($at && new \DateTime($upload->created_at) < $at['currentTime']) {
                $upload->forceDelete();
                return;
            }

            $upload->delete();
            // First in regular type
            $spaces = Space::available()
                ->whereUserId($this->user->id)
                ->orderBy('type')
                ->get();

            foreach ($spaces as $space) {
                // Fill space_available til it is full
                $usedSpace = $space->space - $space->space_available;

                if ($size > $usedSpace) {
                    $space->increment('space_available', $usedSpace);
                    $size = $size - $usedSpace;
                } else {
                    $space->increment('space_available', $size);
                    break;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            $username = $this->user->username;
            Log::error("ERROR: User: {$username}: Adding space failed: " . $e->getMessage());
            DB::rollBack();
        }
    }
}
