<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feed;

class FeedRemovalEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string $username
     */
    public $username;

    /**
     * @var string $feedId
     */
    public $feedId;

    /**
     * @var array $domain
     */
    public $domain;

    /**
     * @var string|null
     */
    public $spotifyUri = null;

    /**
     * Create a new event instance.
     *
     * @param  Feed  $feed
     */
    public function __construct(Feed $feed)
    {
        $this->username = $feed->username;
        $this->feedId = $feed->feed_id;
        $this->domain = $feed->domain;

        if (isset($feed->settings) && isset($feed->settings['spotify_uri']) && !empty($feed->settings['spotify_uri'])) {
            $this->spotifyUri = $feed->settings['spotify_uri'];
        }
    }
}
