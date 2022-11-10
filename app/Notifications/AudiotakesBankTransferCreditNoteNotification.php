<?php

namespace App\Notifications;

use App\Models\AudiotakesBankTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;

class AudiotakesBankTransferCreditNoteNotification extends Notification
{
    use Queueable;

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

/*        if (!$this->file) {
            return [];
        }*/
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

        return $mail
            ->bcc('bastian@audiotakes.net', 'Bastian Albert')
            ->from('billing@audiotakes.net', 'audiotakes GmbH')
            ->template('emails.audiotakes.bank-transfer-credit-note')
            ->subject(trans('audiotakes.mail_bank_transfer_credit_note_subject'))
            ->greeting(trans('audiotakes.mail_bank_transfer_credit_note_greeting'))
            ->line(trans('audiotakes.mail_bank_transfer_credit_note_intro'))
            ->action(trans('audiotakes.mail_bank_transfer_credit_note_button'), route('audiotakes.index'))
            ->line(trans('audiotakes.mail_bank_transfer_credit_note_outro'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Datum
        // Vermarktung Podcasts
        // Wir schreiben Ihnen vereinbarungsgemäß folgenden Betrag aus unserem Vermarktungsvertrag gut.
        // Bezeichnung | Betrag
        // Privatperson: Es wird gemäß §19 Absatz 1 Umsatzsteuergesetz keine Umsatzsteuer gutgeschrieben.
        // EU-Ausland: Es wird gemäß Reverse-Charge-Verfahren wird keine Umsatzsteuer erhoben.

        return [
            //
        ];
    }

    private function getFile(AudiotakesBankTransfer $notifiable)
    {
        // Attach bill if billing contact is available
        $file = storage_path(AudiotakesBankTransfer::CN_STORAGE_DIR . get_user_path($notifiable->user->username)) . DIRECTORY_SEPARATOR . $notifiable->billing_number . AudiotakesBankTransfer::CN_EXTENSION;

        if (File::exists($file)) {
            return $file;
        }

        $file = $notifiable->saveBill();

        if ($file && File::exists($file) && File::isFile($file) && File::isReadable($file)) {
            return $file;
        }

        return null;
    }
}
