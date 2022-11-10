<?php
/**
 * User: fabio
 * Date: 04.05.19
 * Time: 08:58
 */

namespace App\Classes;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserPayment;

class PaymentLegacy extends LegacyBase
{

    /**
     * FeedWriterLegacy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->rootDir . '/modules/podcaster/classes/PaymentDAO.php';
        require_once $this->rootDir . '/lib/SGL/Translation3.php';

        try {
            \SGL_Translation3::singleton('array')->loadDefaultDictionaries();
        } catch(\Exception $e) {
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * @param  User  $user
     * @param $amount
     * @param  string  $currency
     * @return bool
     */
    public function addPaymentByBill(User $user, $amount, $currency = 'EUR'): bool
    {
        $paymentDAO = \PaymentDAO::singleton();

        return $paymentDAO->add($user->usr_id, $user->usr_id, $amount, $currency,
                UserPayment::PAYMENT_METHOD_BILL, 1, trans('accounting.text_payment_by_bill_comment'), 0,
                UserPayment::IS_UNPAID);
    }

    /**
     * @param  int  $receiverId
     * @param  int  $payerId
     * @param  float  $amount
     * @param  string  $currency
     * @param  int  $paymentMethod
     * @param  int  $isRefundable
     * @param  null  $comment
     * @param  int  $isPaid
     * @return bool
     */
    public function addPayment(int $receiverId, int $payerId, float $amount, string $currency, int $paymentMethod,
        int $isRefundable = 1, $comment = null, $isPaid = UserPayment::IS_PAID): bool
    {
        return \PaymentDAO::singleton()->add($receiverId, $payerId, $amount, $currency, $paymentMethod, $isRefundable, $comment,
            0 /** $fee */, $isPaid);
    }

    public function getBill(int $userId, string $paymentId)
    {
        return \PaymentDAO::singleton()->createBill($userId, $paymentId);
    }
}
