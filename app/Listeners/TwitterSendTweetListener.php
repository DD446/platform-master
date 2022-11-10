<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Classes\Datacenter;
use App\Models\Feed;
use App\Models\Show;
use App\Models\User;
use App\Models\UserOauth;

class TwitterSendTweetListener implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @throws \Exception
     */
    public function handle($event)
    {
        $username = $event->username;
        $user = User::whereUsername($username)->first();

        if (!$user) {
            return;
        }

        $userId = $user->id;
        $approvals = UserOauth::where('user_id', '=', $userId)
            ->whereService(UserOauth::SERVICE_TWITTER)
            ->get();

        if ($approvals->count() > 0) {
            $feed = Feed::whereUsername($username)->whereFeedId($event->feedId)->first();

            if (!$feed) {
                return;
            }

            $entries = $feed->entries;
            $settings = $feed->settings;
            $show = null;

            foreach($entries as $key => $entry) {
                // Find correct entry / show
                if ($entry['guid'] == $event->guid) {
                    $show = $entry;

                    // If show is draft or scheduled for later
                    if (!isset($show['is_public']) || !in_array($show['is_public'], [Show::PUBLISH_NOW, Show::PUBLISH_PAST])) {
                        return;
                    }

                    break;
                }
            }

            // Show is not found
            if(!$show) {
                return;
            }

            $category = null;

            // Add hash-tags from categories as nice extra for bigger packages
            if ($user->package_id > 1) {
                $itunesCategories = Datacenter::getItunesCategories();

                if (isset($feed->itunes['category'][0])
                    && array_key_exists($feed->itunes['category'][0], $itunesCategories)) {
                    $cat = mb_strtolower($itunesCategories[$feed->itunes['category'][0]]);
                    $cat = str_replace(' ', '', $cat);

                    if (Str::contains($cat, ':')) {
                        // Convert internal usage, e.g. Arts:Design to arts #design
                        $cat = str_replace(':', ' #', $cat);
                    }
                    if (Str::contains($cat, '&')) {
                        // Convert internal usage, e.g. Society & Culture to society #culture
                        $cat = str_replace('&', ' #', $cat);
                    }
                    if (Str::contains($cat, ' und ')) {
                        // Convert internal usage, e.g. Society & Culture to society #culture
                        $cat = str_replace(' und ', ' #', $cat);
                    }
                    $category = '#' . $cat;
                }
            }

            if (isset($show) && isset($show['tweet_sent']) && $show['tweet_sent']) {
                Log::debug("User {$feed->username}: Tweet already sent.");
                return;
            }

            if (isset($show) && $show && isset($show['show_media']) && !empty($show['show_media'])) {
                $file = get_file($username, $show['show_media']);
                $logo = $logoname = null;

                // TODO: Replace with real feature check
                if ($user->package_id > 2) {
                    if (isset($show['logo']) && !empty($show['logo'])) {
                        $logo = get_file($username, $show['itunes']['logo']);
                    } else {
                        if (isset($feed->logo['itunes'])) {
                            $logo = get_file($username, $feed->logo['itunes']);
                        }
                    }
                    if ($logo && isset($logo['name'])) {
                        $logoname = $logo['name'];
                        $logomime = 'image/'.Str::afterLast($logoname, '.');
                    }
                }

                if ($file) {
                    $filename = $file['name'];
                    // TODO: Replace with get_social_uri OR get_direct_uri with type `social`
                    // which needs new URLs in configs and analytics or use parameters
                    $url = get_direct_uri($username, 'download', $filename, 'twitter');

                    foreach ($approvals as $approval) {
                        // Check that approval is matched with feed
// TODO: This is buggy!!!
                        if (isset($settings['approvals']) &&
                            isset($settings['approvals']['settiungs']) &&
                            !in_array($approval->id, $settings['approvals']['twitter']) &&
                            !in_array($approval->screen_name, $settings['approvals']['twitter'])) {
                                continue;
                        }

                        $oToken = unserialize($approval->oauth_token);

                        if ($oToken instanceof \League\OAuth1\Client\Credentials\TokenCredentials) {
                            $twitter = new \Laminas\Twitter\Twitter([
                                'access_token' => [
                                    'token' => $oToken->getIdentifier(),
                                    'secret' => $oToken->getSecret(),
                                ],
                                'oauth_options' => [
                                    'consumerKey' => config('services.twitter.client_id'),
                                    'consumerSecret' => config('services.twitter.client_secret'),
                                ],
/*                                'http_client_options' => [
                                ],*/
                            ]);

                            $message = trans('feeds.text_tweet_show', [
                                'showTitle' => $show['title'], 'channelTitle' => $feed->rss['title'], 'url' => $url,
                                'category' => $category
                            ]);

                            $message = $this->getFitMessage($message);

                            if ($logoname && in_array($feed->username, [ 'beispiel', 'bzjdis'])) {
                                $mediaIds = [];
                                try {
                                    $image = new \Laminas\Twitter\Image($logo['path'], $logomime);
                                    $iresponse = $image->upload($twitter->getHttpClient());
                                    $mediaIds = ['media_ids' => [$iresponse ? $iresponse->media_id : null]];
                                } catch (\Exception $e) {
                                    $iresponse = null;
                                }

                                $response = $twitter->statusesUpdate(
                                    $message,
                                    null,
                                    $mediaIds
                                );
                            } else {
                                $response = $twitter->statusesUpdate($message);
                            }

                            Log::debug("User {$feed->username}: Tweet sent." . ($response->isError() ? ' ERROR: ' . print_r($response->getErrors(), true) : ''));

                            unset($twitter);
                        }

                        if (isset($response) && $response && $response->isSuccess()) {
                            $entries = $feed->entries;

                            foreach ($entries as $key => &$entry) {
                                if ($entry['guid'] == $show['guid']) {
                                    $entry['tweet_sent'] = $response->toValue();
                                    break;
                                }
                            }
                            $feed->whereUsername($username)
                                ->whereFeedId($event->feedId)
                                ->update([
                                    'entries' => array_values($entries)
                                ]);

                            unset($response);
                        }

                        unset($oToken);
                    }
                }
            }
        }
    }

    /**
     * @param  string  $message
     * @param  int  $maxLength
     * @return string
     */
    private function getFitMessage(string $message, int $maxLength = 280): string
    {
        while(Str::length($message) > $maxLength) {
            if (Str::contains($message, '#')) {
                $message = Str::beforeLast($message, '#');
            } else {
                $message = Str::substr($message, 0, $maxLength-1);
            }
            return $this->getFitMessage($message, $maxLength);
        }

        return $message;
    }
}
