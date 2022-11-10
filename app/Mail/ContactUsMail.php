<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Models\ContactUs;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var ContactUs
     */
    public $contactUs;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactUs $contactUs)
    {
        $this->contactUs = $contactUs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.contact_us')
            ->replyTo($this->contactUs->email, $this->contactUs->name)
            ->from(config('mail.contactus'), $this->contactUs->name)
            ->subject(trans('contact_us.mail_subject', ['name' => $this->contactUs->name, 'type' => trans('contact_us.enquiry.' . $this->contactUs->enquiry_type)]));
    }
}
