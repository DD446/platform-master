<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelShowImportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var mixed|null
     */
    protected $feedId;

    /**
     * @var mixed|null
     */
    public $state;

    /**
     * @var mixed|null
     */
    public $title;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $args)
    {
        $this->feedId = array_shift($args);
        $this->state = array_shift($args);
        $this->title = array_shift($args);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel.show.import.' . $this->feedId);
    }
}
