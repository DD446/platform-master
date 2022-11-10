<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Scopes\IsActiveScope;

class SyncDeletedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:sync-deleted-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates deleted_at entry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::withoutGlobalScope(IsActiveScope::class)
            ->where('email', 'like', 'DELETED_%')
            ->whereNull('deleted_at')
            ->select(['usr_id', 'email', 'deleted_at'])
            ->get();

        $this->line("Found {$users->count()} matches.");

        $failed = 0;

        foreach ($users as $user) {
            $e = explode('_', $user->email);
            $user->deleted_at = Carbon::createFromTimestamp($e[2], 'Europe/Berlin');
            if (!$user->save()) {
                $failed++;
            }
        }

        if ($failed > 0) {
            $this->warn("Failed to update {$failed} users.");
        } else {
            $this->line("Updated all users");
        }
    }
}
