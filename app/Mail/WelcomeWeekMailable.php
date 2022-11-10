<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeWeekMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $state = $this->user->welcome_email_state;

        if ($state < 1 || $state > 7) {
            throw new \OutOfRangeException();
        }

        $markdown = $this->markdown('emails.welcomeweek.day' . $this->user->welcome_email_state, [
            'salutation' => trans_choice('mail.welcome_week_salutation', $state, ['state' => $state]),
            'intro' => trans_choice('mail.welcome_week_intro', $state, ['name' => config('app.name')]),
            'userId' => $this->user->id,
            'hash' => sha1($this->user->id . $this->user->created_at)
        ]);
        $markdown->subject(trans_choice('mail.welcome_week_subject', $state, ['state' => $state, 'service' => config('app.name')]));

        return $markdown;
    }
}
