<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Models\User;

class FileSavedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $file;

    /**
     * @var bool|null
     */
    public ?bool $isImport = false;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  array  $file
     */
    public function __construct(User $user, array $file, ?bool $isImport = false)
    {
        $this->user = $user;
        $this->file = $file;
        $this->isImport = $isImport;
    }
}
