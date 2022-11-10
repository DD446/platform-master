<?php

namespace App\Notifications;

use App\Classes\PaymentLegacy;
use App\Classes\UserPaymentManager;
use App\Models\UserPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;

class UserPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable, Notifiable;

    private $file = null;

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
        $this->file = $this->getFile($notifiable);

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
        $mail = new MailMessage();

        if ($this->file) {
            $mail->attach($this->file, [
                'as' => File::basename($this->file),
                'mime' => 'application/pdf',
            ]);
        }
        $siezen = 0;

        if (!empty($notifiable->payer->userbillingcontact->organisation)) {
            $siezen = 5;
        }

        return $mail
            ->subject(trans('bills.notification_payment_subject', ['id' => $notifiable->bill_id, 'state' => trans_choice('bills.spoken_state', $notifiable->is_paid)]))
            ->greeting(trans_choice('bills.notification_funds_greetings', $siezen, ['first_name' => $notifiable->payer->userbillingcontact->first_name ?? $notifiable->payer->first_name ?? '', 'last_name' => $notifiable->payer->userbillingcontact->last_name ?? $notifiable->payer->last_name ?? '']))
            ->line(trans('bills.notification_payment_text', ['funds' => $notifiable->amount . ' ' . $notifiable->currency]))
            ->action(trans('bills.notification_payment_action'), route('rechnung.index'))
            ->when( $notifiable->is_paid < UserPayment::STATUS_CLOSED, function ($mail) use ($siezen) {
                return $mail->line(trans_choice('bills.notification_payment_pay_bill', $siezen));
            })
            ->line(trans_choice('bills.notification_payment_outro', $siezen))
            ->salutation(trans_choice('bills.notification_payment_salutation', $siezen));
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

    private function getFile(UserPayment $notifiable)
    {
        // Abort if user has not opted-in
        if (!isset($notifiable->payer->userbillingcontact->bill_by_email) || !$notifiable->payer->userbillingcontact->bill_by_email) {
            return null;
        }

        // Attach bill if billing contact is available
        $file = storage_path(UserPayment::BILLS_STORAGE_DIR . get_user_path($notifiable->payer->username)) . DIRECTORY_SEPARATOR . $notifiable->bill_id . UserPayment::BILL_EXTENSION;

        if (File::exists($file)) {
            return $file;
        }

        $contact = $notifiable->payer->userbillingcontact;

        if ($contact) {
            $canDownloadBills = $contact->last_name
                && $contact->street
                && $contact->housenumber
                && $contact->post_code
                && $contact->city
                && $contact->country;
            if ($canDownloadBills) {
                // Generate missing bill
                //$file = $oPayment->getBill($notifiable->payer->user_id, $notifiable->bill_id);
                $file = (new UserPaymentManager)->saveBill($notifiable);

                if ($file && File::exists($file) && File::isFile($file) && File::isReadable($file)) {
                    return $file;
                }
            }
        }

        return null;
    }
}
