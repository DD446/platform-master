<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Classes\Activity;

class ChargeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $siezen = 0;
        $actionText = trans('bills.notification_funds_button_text');
        $actionUrl = route('accounting.index');

        if (!empty($notifiable->user->userbillingcontact->organisation)) {
            $siezen = 5;
        }

        $extraMessage = null;

        if ($notifiable->user->funds < 0) {
            $extraMessage .= trans_choice('bills.notification_add_funds', $siezen);
            $actionText = trans('bills.notification_button_add_funds');
            $actionUrl = route('accounting.create');
        }

        return (new MailMessage)
                ->subject(trans('bills.notification_funds_subject'))
                ->greeting(trans_choice('bills.notification_funds_greetings', $siezen, ['first_name' => $notifiable->user->userbillingcontact->first_name ?? $notifiable->user->first_name ?? '', 'last_name' => $notifiable->user->userbillingcontact->last_name ?? $notifiable->user->last_name ?? '']))
                //->replyTo(config('app.re'))
                ->line(trans_choice('bills.notification_funds_message', $siezen, ['description' => $notifiable->activity_description, 'amount' => change_prefix($notifiable->amount), 'currency' => $notifiable->currency, 'funds' => $notifiable->user->funds]) )
                ->line($extraMessage)
                ->action($actionText, $actionUrl)
                ->salutation(trans_choice('bills.notification_funds_salutation', $siezen, ['service' => config('app.name')]))
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
            //
        ];
    }
}
