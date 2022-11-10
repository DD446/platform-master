<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShowAddEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var string $username
     */
    public $username;

    /**
     * @var string $feedId
     */
    public $feedId;

    /**
     * @var string $guid
     */
    public $guid;

    /**
     * Create a new event instance.
     *
     * @param  string  $username
     * @param  string  $feedId
     * @param  string  $guid
     */
    public function __construct(string $username, string $feedId, string $guid)
    {
        \Illuminate\Support\Facades\Log::debug("User `{$username}`: Received 'ShowAddEvent' for feed `{$feedId}`, guid `{$guid}`");

        $this->username = $username;
        $this->feedId = $feedId;
        $this->guid = $guid;
    }
}
