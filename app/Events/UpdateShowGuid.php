<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateShowGuid
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    public $username;

    public $fileId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $username, $fileId)
    {
        $this->username = $username;
        $this->fileId = $fileId;
    }
}
