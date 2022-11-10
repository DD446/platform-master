<?php

namespace App\Listeners;

use App\Events\UserDeletedEvent;
use App\Models\Feed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use podcasthosting\PodcastClientSpotify\Delivery\Client;
use podcasthosting\PodcastClientSpotify\Exceptions\AuthException;

class DeleteUserPodcastsFromSpotifyListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(UserDeletedEvent $event)
    {
        $user = $event->user;

        foreach (Feed::whereUsername($user->username)->get() as $feed) {
            if (isset($feed->settings)
                && isset($feed->settings['spotify_uri'])
                && !empty($feed->settings['spotify_uri'])) {

                $dc = new Client(config('services.spotify.token'));

                try {
                    if ($dc->remove($feed->settings['spotify_uri'])) {
                        Log::debug("User {$user->username}: Successfully removed entry `{$feed->settings['spotify_uri']}` from Spotify.");
                    }
                } catch (AuthException $e) {
                    Log::debug("User {$user->username}: Failed removing entry `{$feed->settings['spotify_uri']}` from Spotify (AuthException).");
                } catch (\Exception $e) {
                    Log::debug("User {$user->username}: Failed removing entry `{$feed->settings['spotify_uri']}` from Spotify (Exception).");
                }
            }
        }
    }
}
