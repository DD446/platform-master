<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class ContactUsMail extends Notification implements ShouldQueue
{
    use Queueable/*, Dispatchable, InteractsWithQueue*/;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting(trans('contact_us.greeting', ['name' => $notifiable->name]))
                    ->replyTo($notifiable->email, $notifiable->name)
                    //->from($notifiable->email, $notifiable->name)
                    ->from(config('mail.from.address'), $notifiable->name)
                    ->subject(trans('contact_us.mail_subject', ['name' => $notifiable->name, 'type' => trans('contact_us.enquiry.' . $notifiable->enquiry_type)]))
                    ->line($notifiable->comment)
                    ->line($notifiable->name . ' <' . $notifiable->email . '>')
                    ->line($notifiable->phone)
                    //->action('Notification Action', url('/'))
                    ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'name' => $notifiable->name,
            'email' => $notifiable->email,
            'comment' => $notifiable->comment,
            'enquiry_type' => trans('contact_us.enquiry.' . $notifiable->enquiry_type),
        ];
    }
}
