<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class FeedUpdateEvent
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
     * Create a new event instance.
     *
     * @param  string  $username
     * @param  string  $feedId
     */
    public function __construct(string $username, string $feedId)
    {
        Log::debug("User '{$username}': FeedUpdateEvent for feed '{$feedId}' called.");

        $this->username = $username;
        $this->feedId = $feedId;
    }
}
