<?php

namespace App\Http\Controllers;

use App\Classes\UserPaymentManager;
use App\Models\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Classes\PaymentLegacy;
use App\Models\User;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (Gate::forUser(auth()->user())->denies('paymentStoreBill')) {
            abort(403);
        }

        $validated = $this->validate($request, [
            'amount' => 'required|numeric|min:10'
        ], [], [
            'amount' => trans('accounting.validation_amount')
        ]);

        //$pl = new PaymentLegacy();
        // TODO: I18N currency - pass from Vue
        /** @var User $user */
        $user = auth()->user();
        $msg = trans('accounting.message_success_payment_by_bill');

        // TODO: Replace with new user payment method
        //if (!$pl->addPaymentByBill($user, $request->amount)) {

        $upm = new UserPaymentManager();
        $userPayment = $upm->add($user, auth()->user()->id, $validated['amount'], 'EUR', UserPayment::PAYMENT_METHOD_BILL, UserPayment::FUNDS_REFUNDABLE, trans('accounting.text_payment_by_bill_comment'), 0, UserPayment::IS_UNPAID);

        if (!$userPayment) {
            $msg = trans('accounting.message_error_payment_by_bill');
        }

        return response()->json($msg);
    }
}
