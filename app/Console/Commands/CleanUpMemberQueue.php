<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MemberQueue;

class CleanUpMemberQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:clean-up-member-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes invitation that are older than one week';

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
        $this->line("Checking for outdated invitations");
        $res = MemberQueue::where('created_at', '<=', Carbon::now()->subWeek()->toDateTimeString())->delete();
        $this->line("Found {$res} outdated invitations which were deleted.");
    }
}
