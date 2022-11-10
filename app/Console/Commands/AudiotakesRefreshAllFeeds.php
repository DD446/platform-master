<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Console\Command;

class AudiotakesRefreshAllFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:refresh-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renews all audiotakes customer feeds';

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
        if ($this->confirm('Sollen alle Feeds von audiotakes Kunden aktualisiert werden?')) {
            $contracts = AudiotakesContract::with([
                    'user' => function($query) {
                        $query->select(['usr_id', 'username']);
                    }
                ])
                ->select(['user_id', 'feed_id'])
                ->whereNotNull('feed_id')
                ->cursor();

            foreach ($contracts as $contract) {
                if ($contract->user) {
                    try {
                        $this->line("User `{$contract->user->username}`: Renewing feed with id `{$contract->feed_id}`");
                        refresh_feed($contract->user->username, $contract->feed_id);
                    } catch (\Exception $e) {
                        $this->error("User `{$contract->user->username}`: Feed with id `{$contract->feed_id}` not found");
                    }
                }
            }
        }

        return 0;
    }
}
