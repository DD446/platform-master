<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\UserBillingContact;
use Illuminate\Http\Request;

class UserBillingContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {

            $contact = UserBillingContact::firstOrNew(['user_id' => auth()->id()]);

            return response()->json($contact);
        }

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $countries = \Countrylist::getList('de');
        $validated = $this->validate($request, [
            'first_name' => 'nullable',
            'last_name' => 'required|string',
            'email' => 'email|nullable|required_if:bill_by_email,true',
            'bill_by_email' => 'boolean',
            'street' => 'required|string',
            'housenumber' => 'required',
            'post_code' => 'required',
            'city' => 'required|string',
            'country' => [
                'required',
                Rule::in(array_keys($countries)),
            ],
            'organisation' => ['nullable'],
            'department' => ['nullable'],
            'vat_id' => [
                function($attribute, $value, $fail) {
                    if ($value) {
                        if (!(new \Ddeboer\Vatin\Validator())->isValid($value)) {
                            $fail(trans('bills.validation_error_vat_id'));
                        }
                    }
                }
            ],
            'extras' => ['nullable'],
        ], [], [ // TODO: I18N
            'email' => 'E-Mail-Adresse',
            'bill_by_email' => 'Rechnung als Anhang',
            'post_code' => 'Postleitzahl',
            'housenumber' => 'Hausnummer',
            'vat_id' => 'Umsatzsteuer-ID',
        ]);

        $contact = UserBillingContact::owner()->firstOrNew(['user_id' => auth()->id()]);

        if (!$contact->fill($validated)->save()) {
            throw new \Exception(trans('bills.error_saving_billing_contact'));
        }
        $msg = ['message' => trans('bills.success_saving_billing_contact')];

        if (isset($validated['organisation']) && $validated['organisation']) {
            auth()->user()->update(['can_pay_by_bill' => true]);
        }

        return response()->json($msg);
    }
}
