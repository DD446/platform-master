<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Scopes\IsActiveScope;

class PreDeleteExpiredTrialUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:pre-delete-expired-trial-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Soft deletes users who have not added funds and have not activated their account';

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
        $users = User::customer()
            ->whereRaw('? > `date_trialend` + INTERVAL 6 WEEK', [now()])
            ->where('has_paid', '=', 0)
            ->where('funds', '<', 0)
            ->where('role_id', '=', User::ROLE_USER)
            ->select(['username', 'email'])
            ->get();

        $this->line("Found " . $users->count() . " users to delete");
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $this->line('Deleteing user: ' . $user->username . ' <' . $user->email . '>');

            if ($user->delete()) {
                $bar->advance();
            }
        }
        $bar->finish();
    }
}
