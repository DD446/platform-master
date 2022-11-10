<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Classes\Activity;

class RefundNotification extends Notification implements ShouldQueue
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
        $extraMessage = null;

        if ($notifiable->activity_characteristic == 5) {
            $extraMessage = trans('bills.notification_pay_bill');
        }

        if ($notifiable->user->funds < 0) {
            if (!is_null($extraMessage)) {
                $extraMessage .= PHP_EOL . PHP_EOL;
            }
            $extraMessage .= trans_choice('bills.notification_add_funds', $notifiable->activity_characteristic);
        }

        return (new MailMessage)
                ->subject(trans('bills.notification_funds_subject'))
                ->greeting(trans_choice('bills.notification_funds_greetings', $notifiable->activity_characteristic, ['first_name' => $notifiable->user->userbillingcontact->first_name ?? $notifiable->user->first_name ?? '', 'last_name' => $notifiable->user->userbillingcontact->last_name ?? $notifiable->user->last_name ?? '']))
                //->replyTo(config('app.re'))
                ->line(trans_choice('bills.notification_refund_message', $notifiable->activity_characteristic, ['description' => $notifiable->activity_description, 'amount' => change_prefix($notifiable->amount), 'currency' => $notifiable->currency, 'funds' => $notifiable->user->funds]) )
                ->action(trans('bills.notification_funds_button_text'), route('accounting.create'))
                ->line($extraMessage)
                ->salutation(trans_choice('bills.notification_funds_salutation', $notifiable->activity_characteristic, ['service' => config('app.name')]))
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
