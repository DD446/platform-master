<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use Illuminate\Console\Command;

class AudiotakesCreateIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:create-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a list of unique ids and stores them in the database';

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
        $t = time();
        for ($i=0;$i<100;$i++) {
            $identifier = AudiotakesContract::saveNewIdentifier();
            $this->line("Added new identifier: $identifier");
            file_put_contents(storage_path() . DIRECTORY_SEPARATOR . "audiotakes_ids_list_{$t}.csv", $identifier . PHP_EOL, FILE_APPEND);
        }
        return 0;
    }

}
