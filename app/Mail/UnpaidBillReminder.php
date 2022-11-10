<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use App\Classes\PaymentLegacy;
use App\Models\User;
use App\Models\UserPayment;

class UnpaidBillReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var UserPayment
     */
    private UserPayment $payment;

    /**
     * Create a new message instance.
     *
     * @param  UserPayment  $payment
     */
    public function __construct(UserPayment $payment)
    {
        //
        $this->payment = $payment;
        $this->user = $payment->payer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $intro = trans('bills.mail_intro_unpaid_bill_reminder_friendly');
        $outro = trans('bills.mail_hint_outro_unpaid_bill_reminder_friendly');
        $extra = null;

        switch ($this->payment->state) {
            case 0:
                $subject = trans('bills.mail_subject_unpaid_bill_reminder_friendly', ['bill' => $this->payment->bill_id]);
                $extra = trans('bills.mail_info_automated_reminder');
                break;
            case 1:
                $subject = trans('bills.mail_subject_unpaid_bill_reminder_firm', ['bill' => $this->payment->bill_id]);
                $intro = trans('bills.mail_intro_unpaid_bill_reminder_firm');
                $outro = trans('bills.mail_hint_outro_unpaid_bill_reminder_firm');
                $extra = trans('bills.mail_info_automated_reminder');
                break;
            case 2:
                $subject = trans('bills.mail_subject_unpaid_bill_reminder_second_monition', ['bill' => $this->payment->bill_id]);
                $intro = trans('bills.mail_intro_unpaid_bill_reminder_second_monition');
                $outro = trans('bills.mail_hint_outro_unpaid_bill_reminder_second_monition');
                break;
            case 3:
                $subject = trans('bills.mail_subject_unpaid_bill_reminder_last_monition', ['bill' => $this->payment->bill_id]);
                $intro = trans('bills.mail_intro_unpaid_bill_reminder_last_monition');
                $outro = trans('bills.mail_hint_outro_unpaid_bill_reminder_last_monition');
                break;
        }

        $name = $this->user->userbillingcontact->last_name ?? $this->user->last_name;
        $salutation = trans('bills.mail_salutation_unpaid_bill_reminder', ['name' => $name]);

        if (!$name) {
            $salutation = trans('bills.mail_salutation_unpaid_bill_reminder_no_name');
        }

        $mail = $this->markdown('emails.unpaid_bill_reminder', [
                'payment' => $this->payment,
                'salutation' => $salutation,
                'intro' => $intro,
                'outro' => $outro,
                'extra' => $extra,
        ])
            ->subject($subject);

        // Attach bill if billing contact is available
        $filename = storage_path(UserPayment::BILLS_STORAGE_DIR . get_user_path($this->user->username) . DIRECTORY_SEPARATOR . $this->payment->bill_id . UserPayment::BILL_EXTENSION);

        if (File::exists($filename)) {
            $mail->attach($filename);
        } else {
            $contact = $this->user->userbillingcontact;

            if ($contact) {
                $canDownloadBills = $contact->last_name
                    && $contact->street
                    && $contact->housenumber
                    && $contact->post_code
                    && $contact->city
                    && $contact->country;
                if ($canDownloadBills) {
                    // Generate missing bill
                    $oPayment = new PaymentLegacy();
                    $file = $oPayment->getBill($this->user->user_id, $this->payment->bill_id);

                    if ($file && File::exists($file) && File::isFile($file) && File::isReadable($file)) {
                        $mail->attach($filename);
                    }
                }
            }
        }

        $this->payment->increment('state');

        return $mail;
    }
}
