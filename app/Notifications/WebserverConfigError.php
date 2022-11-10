<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WebserverConfigError extends Notification implements ShouldQueue
{
    use Queueable, Notifiable;

    private $output;
    private $status;
    private $ret;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($output, $status, $ret)
    {
        $this->output = $output;
        $this->status = $status;
        $this->ret = $ret;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->subject("Webserver-Reload ist fehlgeschlagen")
                    ->line('Ausgabe: ' . serialize($this->output))
                    ->line('Status-Wert: ' . $this->status)
                    ->line('Rückgabe-Wert: ' . $this->ret);
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
            //
        ];
    }
}
