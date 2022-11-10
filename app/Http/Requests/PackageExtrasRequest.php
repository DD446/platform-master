<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\UserExtra;
use App\Rules\FundsAvailableRule;

class PackageExtrasRequest extends FormRequest
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
        return [
            'type' => ['required', 'string', Rule::in(array_keys(UserExtra::getTypes()))],
            'repeating' => ['required', 'boolean'],
            'amount' => ['required', 'numeric', new FundsAvailableRule],
        ];
    }

    public function attributes()
    {
        return [
            'type' => 'Typ',
        ];
    }
}
