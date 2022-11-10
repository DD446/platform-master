<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WelcomeWeekMailer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:welcome-week-mailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends out an email to new users on a daily basis';

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
     * @return int
     */
    public function handle()
    {
        $users = User::where('welcome_email_state', '>', 0)->get();
        $counter = 1;
        $now = now();

        foreach($users as $user) {
            $when = $now->addSeconds(15 * $counter++);
            try {
                Mail::to($user->email)->later($when, new \App\Mail\WelcomeWeekMailable($user));

                if ($user->welcome_email_state < 7) {
                    $user->increment('welcome_email_state');
                } else {
                    $user->decrement('welcome_email_state', 8);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return 0;
    }
}
