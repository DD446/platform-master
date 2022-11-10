<?php
/**
 * User: fabio
 * Date: 04.07.19
 * Time: 11:07
 */

namespace App\FundsManagement\Http\Controllers;

use App\Classes\Activity;
use App\Classes\UserAccountingManager;
use App\Classes\UserPaymentManager;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Classes\PaymentLegacy;
use App\Models\User;
use App\Models\UserPayment;

class FundsController extends Controller
{
    use ValidatesRequests;

    public function findUsers()
    {
        $query = \request('q');
        $users = User::select([DB::raw('usr_id as code'), DB::raw("CONCAT(usr_id, ' - ', username, ' <', email, '>') as label")])
            ->where('username', 'LIKE', '%' . $query . '%')
            ->orWhere('email', 'LIKE', '%' . $query . '%')
            ->orWhere('usr_id', '=', $query)
            ->get();

        return response()->json($users);
    }

    public function users()
    {
        $users = User::select([DB::raw('usr_id as code'), DB::raw("CONCAT(usr_id, ' - ', username, ' <', email, '>') as label")])->get();

        return response()->json($users);
    }

    public function payment(Request $request)
    {
        $this->validate($request, [
            'receiver_id' => 'required|exists:usr,usr_id',
            'payer_id' => 'required|exists:usr,usr_id',
            'amount' => 'required|numeric',
            'refundable' => 'required|boolean',
            'is_paid' => [
                'required',
                Rule::in([UserPayment::IS_PAID, UserPayment::IS_UNPAID]),
            ],
            'currency' => 'required|string',
            'payment_method' => [
                'required',
                Rule::in(array_keys(UserPayment::$paymentMethods)),
            ]
        ], [], [
            'receiver_id' => 'Empfänger',
            'payer_id' => 'Sender',
            'amount' => 'Höhe',
            'refundable' => 'Erstattungsfähig',
            'is_paid' => 'Bezahlt',
            'currency' => 'Währung',
            'payment_method' => 'Zahlart',
        ]);

        $msg = "Die Zahlung wurde erfolgreich gebucht.";
/*        $p = new PaymentLegacy();

        if (!$p->addPayment($request->receiver_id['code'], $request->payer_id['code'], $request->amount, $request->currency, $request->payment_method, $request->refundable, $request->comment, $request->is_paid)) {
            $msg = "Die Zahlung konnte nicht gebucht werden.";

            return response()->json($msg, 500);
        }*/

        $upm = new UserPaymentManager();
        $user = User::where('usr_id', '=', $request->receiver_id)->sole();
        $userPayment = $upm->add($user, $request->payer_id, $request->amount, $request->currency, $request->payment_method, $request->refundable, $request->comment, 0, $request->is_paid);

        if (!$userPayment) {
            $msg = "Die Zahlung konnte nicht gebucht werden.";

            return response()->json($msg, 500);
        }
        return response()->json($msg);
    }

    public function bills()
    {
        $bills = UserPayment::whereIsPaid(UserPayment::IS_UNPAID)->get();

        return response()->json($bills);
    }
}
