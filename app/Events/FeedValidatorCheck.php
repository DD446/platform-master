<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedValidatorCheck implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $uuid;

    /**
     * @var bool
     */
    public $state;

    /**
     * @var mixed
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param $uuid
     */
    public function __construct(array $args)
    {
        $this->uuid = array_shift($args);
        $this->state = array_shift($args);

        if ($this->state == 'error') {
            $this->state = 'danger';
        }

        $this->message = array_shift($args);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('state.check.' . $this->uuid);
    }
}
