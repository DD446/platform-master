<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AudiotakesContractPartnerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $countries = \Countrylist::getList('de');

        return [
            'user.first_name' => ['required', 'string', 'min:2', 'max:50'],
            'user.last_name' => ['required', 'string', 'min:2', 'max:100'],
            'user.email' => ['required', 'email', 'max:32'],
            'user.telephone' => ['nullable', 'max:16'],
            'user.street' => ['required', 'string', 'max:50'],
            'user.housenumber' => ['required', 'string', 'max:10'],
            'user.city' => ['required', 'string', 'max:50'],
            'user.post_code' => ['required', 'max:10'],
            'user.country' =>  [
                'required',
                Rule::in(array_keys($countries)),
            ],
            'contract_partner' => ['required', 'in:private,corporate'],
            'user.organisation' => ['nullable', 'required_if:contract_partner,corporate', 'string', 'max:255'],
            'user.vat_id' => ['nullable'],
        ];
    }
}
