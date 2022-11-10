<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Iban;

class StoreAudiotakesPayoutContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        \Illuminate\Support\Facades\Log::debug($this->get('vat_required'));
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:2'],
            'country' => ['required', 'exists:countries,iso_3166_2'],
            'paypal' => ['nullable', 'required_if:goal_type,paypal', 'email'],
            'iban' => ['nullable', 'required_if:goal_type,bank', new Iban],
            'bank_account_owner' => ['nullable', 'required_if:goal_type,bank', 'min:3'],
            'vat_required' => ['in:0,1'],
            'tax_id' => ['nullable', 'required_if:vat_required,0', 'string'],
            'vat_id' => ['nullable', 'required_if:vat_required,1', 'string', Rule::when($this->get('vat_required') == 1, ['vat_number_format'])],
            'goal_type' => ['required', 'in:paypal,bank'],
        ];
    }

    public function attributes()
    {
        return [
            'paypal' => trans('audiotakes.label_payout_paypal'),
            'iban' => trans('audiotakes.label_payout_iban'),
            'vat_required' => trans('audiotakes.label_vat_required'),
            'vat_number' => trans('validation.vat_number'),
        ];
    }


}
