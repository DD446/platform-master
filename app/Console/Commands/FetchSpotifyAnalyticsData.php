<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Feed;
use App\Models\SpotifyAnalytic;
use App\Models\User;
use podcasthosting\PodcastClientSpotify\Analytics\Client;
use podcasthosting\PodcastClientSpotify\Analytics\Result;
use podcasthosting\PodcastClientSpotify\AuthClient;
use podcasthosting\PodcastClientSpotify\Exceptions\AuthException;

class FetchSpotifyAnalyticsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:fetch-spotify-analytics-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save statistics data from Spotify';

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
        $auth = new AuthClient(config('spotify.client_id'), config('spotify.client_secret'));

        try {
            $token = $auth->getToken();
        } catch (AuthException $e) {
            Log::error("Getting token failed.");
            Log::error($e->getTraceAsString());
            $this->error($e->getMessage());
            exit(-1);
        }

        try {
            $analyticsClient = new Client(config('spotify.client_id'), $token->access_token);

            $this->line("Fetching data...");

            $date = new \DateTime();
            $date->sub(new \DateInterval('P1D'));
            //$date->setDate(2018, 04, 10); // 10.04.18
            $aUsernames = $aDomains = [];

            while ($date <= (new \DateTime())->setTime(0, 0, 0)) {
                $this->line($date->format("d.m.Y"));
                $this->fetch($analyticsClient, $date, $aUsernames, $aDomains);
                $interval = \DateInterval::createfromdatestring('+1 day');
                $date->add($interval);
            }
        } catch (AuthException $e) {
            Log::error("Fetching data failed with token.");
            Log::error($e->getTraceAsString());
            $this->error($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Fetching data failed.");
            Log::error($e->getTraceAsString());
            $this->error($e->getMessage());
        }

        return true;
    }

    private function fetch(Client $analyticsClient, \DateTime $date, array &$aUsernames, array $aDomains)
    {
        try {
            $data = $analyticsClient->get($date);

            if (!($data instanceof Result)) {
                $this->error("Did not get result object");
                exit(-1);
            }

            $list = $data->getRaw();
            $this->line("Found " . count($list) . " rows");

            foreach($list as $line) {
                $o = json_decode($line);

                if (!isset($o->episode)) {
                    //$this->error("Not processable line: " . $line);
                    continue;
                }
                $aUrl = parse_url($o->episode->url);
                $username = null;
                $userId = null;

                if (!isset($aUrl['host'])) {
                    //$this->error("Unparsable URL: " . $o->episode->url);
                    continue;
                }

                if (strpos($aUrl['host'], config('app.domain')) !== false) {
                    $username = Str::before($aUrl['host'], '.' . config('app.domain'));
                } elseif (in_array($aUrl['host'], ['traffic.libsyn.com', 'anchor.fm', 'cdn.podigee.com', 'feeds.soundcloud.com', 'media.blubrry.com', 'aphid.fireside.fm', 'hearthis.at', 'radioglobo.mc.tritondigital.com', 'tracking.feedpress.it', 'radiotalkstorage.blob.core.windows.net', 'awscdn.podcasts.com', 'api.spreaker.com', 'download.djpod.com', 'mcdn.podbean.com', 'mx.ivoox.com', 'www.ivoox.com', 'justcast.herokuapp.com'])) {
                    continue;
                } else {

                    if (array_key_exists($aUrl['host'], $aDomains)) {
                        $username = $aDomains[$aUrl['host']];
                    } else {
                        $username = Feed::select('username')
                            ->where('domain.hostname', 'https://' . $aUrl['host'])
                            ->orWhere('domain.hostname', 'http://' . $aUrl['host'])
                            ->value('username');
                        $aDomains[$aUrl['host']] = $username;
                    }
                }

                if (!$username) {
                    $this->error("Not supported: " . $aUrl['host']);
                    continue;
                }

                if (array_key_exists($username, $aUsernames)) {
                    $userId = $aUsernames[$username];
                } else {
                    $userId = User::select('usr_id')->whereUsername($username)->value('usr_id');
                    $aUsernames[$username] = $userId;
                }

                if (!$userId) {
                    //$this->error("User ID for {$username} not found.");
                    continue;
                }

                $e = explode('/', $aUrl['path']);
                $a = array_filter($e);
                $feedId = array_shift($a);
                $showTitle = $o->episode->name;
                $file = array_pop($a);

                $sa = new SpotifyAnalytic();
                $sa->user_id = $userId;
                $sa->feed_id = $feedId;
                $sa->show_title = $showTitle;
                $sa->file = $file;
                $sa->date = $o->date;
                $sa->version = $o->version;
                $sa->data = $line;
                $sa->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $this->error($e->getMessage());
        }

        return true;
    }
}
