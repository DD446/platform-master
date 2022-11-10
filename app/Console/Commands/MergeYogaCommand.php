<?php

namespace App\Console\Commands;

use App\Jobs\GetShowsForImportedFeed;
use Illuminate\Console\Command;

class MergeYogaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:merge-yoga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        GetShowsForImportedFeed::dispatchSync('jkv3wg', 'bewusst-leben-lexikon');
        return 0;
    }
}
