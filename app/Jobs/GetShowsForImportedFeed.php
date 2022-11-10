<?php

namespace App\Jobs;

use App\Classes\FeedReader;
use App\Classes\MediaManager;
use App\Events\ChannelShowImportEvent;
use App\Events\FeedUpdateEvent;
use App\Models\Feed;
use App\Models\Show;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetShowsForImportedFeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    private Feed $feed;

    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $username, string $feedId)
    {
        $this->user = User::whereUsername($username)->firstOrFail();
        $this->feed = Feed::where('username', '=', $username)
            ->where(function($query) use ($feedId) {
                $query->whereFeedId($feedId)
                    ->orWhere('feed_id', '=', Str::lower($feedId));
            })
            ->firstOrFail();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $feed = $this->feed;
        $user = $this->user;
        $entries = [];
        $remoteFeed = FeedReader::getCachedFeed($feed->settings['is_importing']);
        $aKnownImages = [];

        event(new ChannelShowImportEvent([$feed->feed_id, 'started', $remoteFeed->count()]));

        if ($this->user->username == 'jkv3wg' && $this->feed->feed_id == 'bewusst-leben-lexikon') {
            $i = 0;
        }

        foreach ($remoteFeed as $entry) {
            if ($this->user->username == 'jkv3wg' && $this->feed->feed_id == 'bewusst-leben-lexikon') {
                if ($i > 2000) {
                    break;
                }
            }
            $mm = new MediaManager($user);
            $_entry = [];
            $state = 'success';
            $itunes = $entry->getExtension('Podcast');
            $google = $entry->getExtension('GooglePlayPodcast');
            $podcastindex = $entry->getExtension('PodcastIndex');
            $enc = $entry->getEnclosure();
            $_entry['itunes']['duration'] = $itunes->getDuration();

            $retFile = [];

            if (isset($enc->url) && filter_var($enc->url, FILTER_VALIDATE_URL)) {
                try {
                    if ($this->user->username == 'jkv3wg' && $this->feed->feed_id == 'bewusst-leben-lexikon') {
                        Log::debug("User jkv3wg:  Importing show for 'bewusst-leben-lexikon' feed");
                        $i++;
                        $url = $enc->url;
                        $ext = pathinfo(parse_url($url)['path'])['extension'];
                        $name = pathinfo(parse_url($url)['path'])['filename'];
                        $filename = make_url_safe($name, false) . '.' . $ext;
                        $filename = ltrim($filename, '.');
                        try {
                            $id = get_file_id_by_filename($this->user->username, $filename);

                            if ($id) {
                                $_entry['show_media'] = $id;
                                $_entry['itunes']['duration'] = get_duration($this->user->username, $id);
                            }
                        } catch (\Exception $e) {
                        }
                        /*                        $file = File::glob(storage_path(get_user_media_path($this->user->username)) . DIRECTORY_SEPARATOR . '*' . $originalName);
                                                // We have got a match
                                                if (is_array($file) && count($file) == 0) {
                                                    $cPath = $file[0];
                                                }*/
                    } else {
                        $retFile = $mm->saveFileFromUrl($enc->url, true);

                        if (isset($retFile['file']['id'])
                            && $retFile['statusCode'] === 200) {
                            $_entry['show_media'] = $retFile['file']['id'];
                            // Do not rely on data given from import
                            // Duration given as seconds causes feed generation to fail
                            $_entry['itunes']['duration'] = get_duration($feed->username, $retFile['file']['id']);
                        }
                    }
                } catch (\Exception $e) {
                    $state = 'error';
                    Log::error("User '" . $feed->username . "': Could not import file from url '{$enc->url}' for Feed '{$feed->feed_id}' with: " . $e->getMessage());
                }
                unset($retFile);
            }
            $_entry['guid'] = get_guid('pod-');
            $_entry['title'] = $entry->getTitle();
            $_entry['link'] = $entry->getLink();
            if ($this->user->username == 'jkv3wg' && $this->feed->feed_id == 'bewusst-leben-lexikon') {
                $_entry['description'] = strip_tags($entry->getDescription());
            } else {
                $_entry['description'] = $entry->getDescription() ?? $entry->getContent() ?? $entry->getTitle() ?? 'Podcast';
            }
            $_entry['author'] = $entry->getAuthor() ? $entry->getAuthor()['name'] : 'Podcaster';
            $_entry['lastUpdate'] = $entry->getDateCreated()->getTimestamp();
            $_entry['is_public'] = Show::PUBLISH_NOW;
            $_entry['itunes']['author'] = $itunes->getCastAuthor();
            $_entry['itunes']['email'] = $entry->getXpath()->evaluate('string(' . $entry->getXpathPrefix() . '/itunes:owner/itunes:email)');
            $_entry['itunes']['subtitle'] = $itunes->getSubtitle();
            $_entry['itunes']['summary'] = $itunes->getSummary();
            $_entry['itunes']['block'] = FeedReader::cleanBlock($itunes->getBlock());
            $_entry['itunes']['explicit'] = FeedReader::cleanExplicit($itunes->getExplicit());
            //$_entry['itunes']['isClosedCaptioned' => $itunes->isClosedCaptioned(),
            $_entry['itunes']['season'] = $itunes->getSeason();
            $_entry['itunes']['episode'] = $itunes->getEpisode();
            $_entry['itunes']['episodeType'] = FeedReader::cleanEpisodeType($itunes->getEpisodeType());
            // TODO: Itunes image
            $image = $entry->getXpath()->evaluate('string(//itunes:image/@href)');

            if (isset($image) && filter_var($image, FILTER_VALIDATE_URL)) {
                if (array_key_exists($image, $aKnownImages)) {
                    $_entry['itunes']['logo'] = $aKnownImages[$image];
                } else {
                    $ret = [];
                    try {
                        $ret = $mm->saveFileFromUrl($image, true);

                        if (isset($ret['file']['id'])
                            && $ret['statusCode'] === 200) {
                            $_entry['itunes']['logo'] = $ret['file']['id'];
                            $aKnownImages[$image] = $ret['file']['id'];
                        }
                        unset($ret);
                    } catch (\Exception $e) {
                        Log::error("User '" . $feed->username . "': Could not import image from url '{$enc->url}' for Feed '{$feed->feed_id}' with: " . $e->getMessage());
                    }
                    unset($ret);
                }
            }
            $_entry['googleplay'] = [
                "block" => $google->getPlayPodcastBlock(),
                "description" => $google->getPlayPodcastDescription(),
                "explicit" => $google->getPlayPodcastExplicit(),
            ];
            // TODO: Podcast Index
            $_entry['podcastindex'] = [
                'transcript' => $podcastindex->getTranscript(),
                'chapters' => $podcastindex->getChapters(),
                'soundbites' => $podcastindex->getSoundbites(),
            ];
            $entries[] = $_entry;
            event(new ChannelShowImportEvent([$feed->feed_id, $state, $_entry['title']]));
        }

        $settings = $feed->settings;
        unset($settings['is_importing']);
        $res = $feed
            ->whereUsername($feed->username)
            ->whereFeedId($feed->feed_id)
            ->update(['entries' => $entries, 'settings' => $settings]);

        if ($res) {
            event(new ChannelShowImportEvent([$feed->feed_id, 'finished', $feed->rss['title']]));
        } else {
            event(new ChannelShowImportEvent([$feed->feed_id, 'failed', $feed->rss['title']]));
        }
        event(new FeedUpdateEvent($feed->username, $feed->feed_id));
    }
}
