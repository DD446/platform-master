<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * Create a new message instance.
     *
     * @param  Feedback  $feedback
     */
    public function __construct(){
        $this->feedback = $this;
    }/*Feedback $feedback)
    {
        $this->feedback = $feedback;
    }*/

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.feedback', ['feedback' => $this->feedback])
            ->replyTo($this->feedback->user->email, $this->feedback->user->name)
            ->from(config('mail.contactus'), $this->feedback->user->name)
            ->subject(trans('feedback.mail_subject', ['type' => trans_choice('feedback.type', $this->feedback->type)]));
    }
}
