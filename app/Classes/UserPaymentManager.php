<?php
/**
 * User: fabio
 * Date: 10.08.20
 * Time: 15:45
 */

namespace App\Classes;


use App\Notifications\UserPaymentNotification;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserPayment;
use App\Models\UserPaymentsSeq;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class UserPaymentManager
{
    /**
     * @param  User  $user
     * @param  int  $payerId
     * @param  float  $amount
     * @param  string  $currency
     * @param  string  $paymentMethod
     * @param  int  $refundable
     * @param  string|null  $comment
     * @param  float|int  $fee
     * @param  int  $isPaid
     * @return bool
     */
    public function add(User $user, int $payerId, float $amount, string $currency, string $paymentMethod,
        int $refundable = UserPayment::FUNDS_REFUNDABLE, ?string $comment = null, float $fee = 0,
        int $isPaid = UserPayment::IS_PAID): ?UserPayment
    {
        $res = null;
        $isAdded = false;

        DB::beginTransaction();

        try {
            UserPaymentsSeq::increment('id');
            $paymentId = UserPaymentsSeq::firstOrFail()->value('id');

            if ($refundable == UserPayment::FUNDS_REFUNDABLE) {
                $billId = $this->getBillId($paymentId);
            } else {
                $billId = '';
            }

            $now = now();
            $userPayment = UserPayment::create([
                'payment_id' => $paymentId,
                'receiver_id' => $user->user_id,
                'payer_id' => $payerId,
                'usr_id' => auth()->user()->user_id ?? 0,
                'amount' => $amount,
                'currency' => $currency,
                'payment_method' => $paymentMethod,
                'date_created' => $now,
                'bill_id' => $billId,
                'is_refundable' => $refundable,
                'is_paid' => $isPaid,
                'comment' => $comment
            ]);

            $uam = new UserAccountingManager();
            $uam->add($user, Activity::FUNDS, $paymentMethod, $amount, null, $currency, $now, null, UserAccounting::STATUS_CLOSED, UserAccounting::PROCEDURE_AUTO);

            if ($user->is_blocked && $user->funds >= 0) {
                $user->is_blocked = false;
                $user->save();
            }

            $isAdded = true;
            DB::commit();
        } catch (\Exception $e) {
            $username = $user->username;
            Log::error("ERROR: User: {$username}: Adding payment failed: " . $e->getMessage());
            DB::rollBack();
        }

        $res = $userPayment;

        // TODO: Use event and/or on user accounting create hook
        // Create bill and save a backup for tax stuff
        // $this->backupBill($this->createBill($payerId, $billId, true, true));

        return $res;
    }

    public function getBillId(int $paymentId): string
    {
        return UserPayment::BILLING_PREFIX . date('Ymd') . $paymentId;
    }

    public function saveBill(UserPayment $payment): string
    {
        $username = $payment->payer->username;
        $this->prepareForBill($payment);
        $filename = storage_path(UserPayment::BILLS_STORAGE_DIR . get_user_path($username)) . DIRECTORY_SEPARATOR . $payment->bill_id. UserPayment::BILL_EXTENSION;
        File::ensureDirectoryExists(File::dirname($filename));
        $hideNav = true;
        $html = view('bills.invoice', compact('payment', 'hideNav'))->render();

        Browsershot::html($html)
            ->setOption('addStyleTag', json_encode(['content' => 'html { -webkit-print-color-adjust: exact; }']))
            ->noSandbox()
            ->format('A4')
            ->save($filename);

        if (!File::exists($filename)) {
            Log::error("ERROR: User: {$username}: Could not create bill at {$filename}.");
            throw new \Exception("ERROR: Could not create bill.");
        }

        return $filename;
    }

    public function prepareForBill(&$payment)
    {
        $oTaxCalculator = new TaxCalculator($payment->payer->userbillingcontact->country, $payment->payer->userbillingcontact->vat_id, $payment->payer->userbillingcontact->post_code);
        $oTaxCalculator->setGross($payment->amount);

        // Hack for temporary reduction of VAT in Germany
        if (strtolower($payment->payer->userbillingcontact->country) == 'de') {
            if (new \DateTime($payment->date_created)
                >= new \DateTime('2020-07-01 00:00:00')
                && new \DateTime($payment->date_created)
                < new \DateTime('2021-01-01 00:00:00')) {
                $oTaxCalculator->setVat(16);
            }
        }
        $payment->amount_gross = $oTaxCalculator->getGross(true);
        $payment->amount_net = $oTaxCalculator->getNet(true);
        $payment->amount_tax = $oTaxCalculator->getTax(true);
        $payment->amount_vat = $oTaxCalculator->getVat();
        $payment->is_reverse_charge = $oTaxCalculator->isReverseCharge();
        $countries = \Countrylist::getList('de'); // TODO: I18N
        $payment->country_spelled = $countries[$payment->payer->userbillingcontact->country];
    }
}
