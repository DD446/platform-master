<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CollectEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:collect-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes customer emails for a specific criterium to a file';

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
        $users = User::notPionier()->where('is_trial', '<>', User::IS_INACTIVE)->where('funds', '>=', '-10.00')->get();

        $this->line('Users count: ' . count($users));

        $csvExporter = new \Laracsv\Export();
        $csvExporter->build($users, ['email', 'first_name', 'last_name']);
        File::put(storage_path('collect_emails.csv'), $csvExporter->getWriter());

        return 0;
    }
}
