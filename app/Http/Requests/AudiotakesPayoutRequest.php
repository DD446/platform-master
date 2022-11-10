<?php

namespace App\Http\Requests;

use App\Classes\AudiotakesManager;
use App\Models\AudiotakesContract;
use App\Models\AudiotakesPayoutContact;
use Illuminate\Foundation\Http\FormRequest;

class AudiotakesPayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check()
            && AudiotakesContract::withTrashed()->owner()->count() > 0
/*            && AudiotakesPayoutContact::owner()->count() > 0*/;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payoutOption' => ['required', 'in:funds,payment'],
            'payoutFunds' => ['required', 'numeric', 'gte:' . AudiotakesContract::MINIMUM_PAYOUT_VALUE, 'lte:' . AudiotakesManager::getFunds(auth()->id())],
            'payoutGoal' => ['nullable', 'required_if:payoutOption,payment', 'exists:audiotakes_payout_contacts,id,user_id,' . auth()->id()],
            'audiotakes_contract_partner_id' => ['nullable', 'required_if:payoutOption,payment', 'exists:audiotakes_contract_partners,id,user_id,' . auth()->id()],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
        ];
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'payoutOption' => trans('audiotakes.label_payout_option'),
            'payoutFunds' => trans('audiotakes.label_payout_value'),
            'payoutGoal' => trans('audiotakes.label_payout_goal'),
        ];
    }
}
