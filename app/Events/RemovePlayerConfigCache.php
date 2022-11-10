<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\PlayerConfig;

class RemovePlayerConfigCache
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlayerConfig
     */
    public $playerConfig;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PlayerConfig $playerConfig)
    {
        $this->playerConfig = $playerConfig;
    }
}
