<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogoSavedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var array
     */
    public array $file;

    /**
     * @var string
     */
    public string $feedId;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  array  $file
     */
    public function __construct(User $user, array $file, string $feedId)
    {
        $this->user = $user;
        $this->file = $file;
        $this->feedId = $feedId;
    }
}
