<?php

namespace App\Console\Commands;

use App\Models\AudiotakesContract;
use App\Models\Feed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * User `gtmaif`: Activating feed with id `motion-confidence`
 */
class AudiotakesAddPodcastsForPodscribe extends Command
{
    const URL = 'https://api.podscribe.adswizz.com';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiotakes:add-podcasts-for-podscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uses the Podscribe API to activate podcasts for transcriptions';

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
        $apiKey = env('PODSCRIBE_API_KEY');

        //if ($this->confirm('Soll alle Feeds für Podscribe aktiviert werden?')) {
            $contracts = AudiotakesContract::with([
                'user' => function($query) {
                    $query->select(['usr_id', 'username']);
                }
            ])
                ->select(['user_id', 'identifier', 'feed_id'])
                ->whereNotNull('feed_id')
                ->cursor();

            foreach ($contracts as $contract) {
                try {
                    $feed = Feed::whereFeedId($contract->feed_id)->whereUsername($contract->user->username)->first();
                    $logo = null;

                    if (isset($feed->settings['audiotakes']) && $feed->settings['audiotakes'] == 1) {
                        $this->line("User `{$contract->user->username}`: Activating feed with id `{$contract->feed_id}`");
                        if (isset($entry['itunes']['logo']) && !empty($entry['itunes']['logo'])) {
                            try {
                                $logo = get_image_uri($feed->feed_id, $entry['itunes']['logo'], $feed->domain);
                            } catch (\Exception $e) {
                            }
                        }

                        $data = [
                            "publisherId" => 472,
                            "awCollectionID" => $contract->identifier,
                            "name" => $feed->rss['title'],
                            "rssUrl" => get_feed_uri($feed->feed_id, $feed->domain),
                            "description" => Str::limit(strip_tags($feed->rss['description']), 497),
                            "coverUrl" => $logo,
                            "rssMonitored" => true,
                            "hostRead" => true,
                            "hostEmail" => 'kontakt@audiotakes.net',
                            "talentRead" => true,
                            "talentEmail" => 'kontakt@audiotakes.net'
                        ];
                        $response = Http::withHeaders(['x-api-key' => $apiKey])->post(self::URL . '/shows', $data);

                        if (!$response->successful()) {
                            $this->error($response->getStatusCode());
                            $this->error($response->clientError());
                            $this->error($response->serverError());
                        }
                    } else {
                        $this->warn("User `{$contract->user->username}`: Skipping feed with id `{$contract->feed_id}`");
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        //}

        return 0;
    }
}
