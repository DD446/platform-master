<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\CreateSpotifyMail;
use App\Models\Feed;
use App\Models\User;
use App\Scopes\OwnerScope;
use podcasthosting\PodcastClientSpotify\DeliveryClient;
use podcasthosting\PodcastClientSpotify\Exceptions\AuthException;
use podcasthosting\PodcastClientSpotify\Exceptions\DomainException;
use podcasthosting\PodcastClientSpotify\Exceptions\DuplicateException;
use podcasthosting\PodcastClientSpotify\Result;

class CreateSpotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:create-spotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add podcasts with opt-in to Spotify';

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
        $feeds = Feed::select('feed_id', 'username', 'domain', 'settings.spotify')
            ->where('settings.spotify', '=', '1')
            ->whereNull('settings.spotify_uri')
            ->orWhereNull('settings.spotify_updated')
            ->get();
        $aUser = User::select('username')->get()->pluck('username')->toArray();
        $added = $updated = $custom = $skipped = 0;
        $aUriAdded = $aUriCustom = [];
        $total = $feeds->count();
        $bar = $this->output->createProgressBar($total);
        $dc = new DeliveryClient(config('services.spotify.token'));

        foreach($feeds as $feed) {
            if (!in_array($feed->username, $aUser)) {
                // Skip inactive/deleted customers
                $skipped++;
                // @TODO: There should be no entries in feed table if customer if already inactive - so remove entries
                //$this->warn("Found info in feed table for inactive user {$feed->username}.");
                continue;
            }

            $uri = get_feed_uri($feed->feed_id, $feed->domain);

            try {
                $res = true;
                //$res = $dc->create($feed->feed_id, $uri);
                //$res = new Result('spotify:show:123');

                if ($res instanceof Result) {
                    $update = Feed::where('username', '=', $feed->username)
                        ->where('feed_id', '=', $feed->feed_id)
                        ->update(['settings.spotify_uri' => $res->getSpotifyUri(), 'settings.spotify_updated' => now()]);
                    if (!$update) {
                        $this->warn("Adding SpotifyUri for user {$feed->username} failed.");
                        $bar->advance();
                        continue;
                    }
                    array_push($aUriAdded, $uri);
                    $added++;
                }
            } catch (AuthException $e) {
                $this->error($e->getMessage());
            } catch (DuplicateException $e) {
                $this->error($e->getMessage());

                $update = Feed::where('username', '=', $feed->username)
                    ->where('feed_id', '=', $feed->feed_id)
                    ->update(['settings.spotify_updated' => now()]);
                if (!$update) {
                    $this->warn("Updating for user {$feed->username} failed.");
                    $bar->advance();
                    continue;
                }
                $updated++;
            } catch (DomainException $e) {
                $this->error($e->getMessage());

                array_push($aUriCustom, $uri);
                $custom++;
            } catch(\Exception $e) {
                $this->error($e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();

        $combined = $added + $updated + $skipped + $custom;
        $diff = $total - $combined;

        $this->line(PHP_EOL);
        $this->info("Added {$added}, Updated {$updated}, Skipped {$skipped}, Custom {$custom}, Total {$combined}/{$total}, Diff {$diff}");

        $aUriLatest = $this->getLatest();

        if ($added > 0 || $custom > 0 || count($aUriLatest) > 0) {
            // Mail list of added feeds as .csv
            Mail::to(['fabio@podcaster.de', 'silviam@spotify.com'])
                ->send(new CreateSpotifyMail($aUriAdded, $aUriCustom, $aUriLatest));
        }
    }

    private function getLatest()
    {
        $this->line(PHP_EOL);
        $this->info("Fetching latest 250 feeds");

        Schema::connection('mongodb')->table('feeds', function($collection)
        {
            $collection->index('settings.spotify_updated');
        });

        $feeds = Feed::select('feed_id', 'domain', 'settings.spotify_updated')
            ->where('settings.spotify', '=', '1')
            ->whereNotNull('settings.spotify_uri')
            ->orderBy('settings.spotify_updated', 'DESC')
            ->take(250)
            ->get();

        $latest = [];


        foreach($feeds as $feed) {
            $dateTime = new \DateTime();
            $date = $dateTime->__set_state($feed->settings['spotify_updated']);

            array_push($latest, [get_feed_uri($feed->feed_id, $feed->domain), $date->format('Y-m-d H:i:s')]);
        }

        return $latest;
    }
}
