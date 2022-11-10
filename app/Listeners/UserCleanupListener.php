<?php

namespace App\Listeners;

use App\Classes\NginxServerConfigWriter;
use App\Events\UserDeletedEvent;
use App\Models\Feed;
use App\Models\PlayerConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UserCleanupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserDeletedEvent  $event
     * @return void
     */
    public function handle(UserDeletedEvent $event)
    {
        $user = $event->user;

        // Delete feed files from disk
        try {
            File::deleteDirectory(storage_path(get_user_feed_path($user->username)));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        // Delete links for public entries from disk
        try {
            File::deleteDirectory(storage_path(get_user_public_path($user->username)));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        // Delete cached files for player from disk
        $configs = PlayerConfig::where('user_id', '=', $user->id)->get();
        foreach($configs as $config) {
            $file = storage_path('app/public/player/config/'.$config->uuid.'.js');
            if (File::exists($file)) {
                try {
                    File::delete($file);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

            if (!empty($config->custom_styles)) {
                $cssFile = storage_path('app/public/player/config/' . $config->uuid . '.css');
                if (File::exists($cssFile)) {
                    try {
                        File::delete($cssFile);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        Log::error($e->getTraceAsString());
                    }
                }
            }
        }

        // Delete log dir
        try {
            File::deleteDirectory($user->getLogfileDir());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }

        // Delete all nginx configs
        $feeds = Feed::where('username', '=', $user->username)->get();
        foreach($feeds as $feed) {
            $ncw = new NginxServerConfigWriter($user->username, "{$feed->domain['subdomain']}.{$feed->domain['tld']}");
            $ncw->delete();
        }

        // Delete ghost files which cause nginx reload problems
        $ncw = new NginxServerConfigWriter($user->username, "zuloeschen.dummy");
        $searchDir = dirname($ncw->getFilename());
        $searchString = '/' . $user->username . '/';
        $result = shell_exec('grep -Ri "'.$searchString.'" '.$searchDir);

        $entries = explode(PHP_EOL, $result);
        foreach ($entries as $entry) {
            // "/var/www/www.podcaster.de/portal//lib/data/hostingstorage/conf/schutz.podcaster.de.conf:\taccess_log /var/log/nginx/hosting/b/e/i/beispiel/access.log main;",
            $lines = explode(":\t", $entry);
            $file = $lines[0];
            if (File::exists($file)) {
                try {
                    File::delete($file);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }
        }
    }
}
