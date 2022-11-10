<?php

namespace App\Console\Commands;

use App\Models\Feed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\PlayerConfig;

class CacheAllPlayerConfigs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:cache-player-configs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and refreshes the cache file for all player configs';

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
        $configs = PlayerConfig::with('user')->get();

        foreach ($configs as $config) {
            if (!$config->user) {
                $config->delete();
                continue;
            }
            try {
                cache_player_config($config);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
