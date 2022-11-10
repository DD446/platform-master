<?php

namespace App\Http\Requests;

use App\Rules\PasswordIsCorrect;
use Illuminate\Foundation\Http\FormRequest;

class UserDeleteRequest extends FormRequest
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
            'password' => ['required', new PasswordIsCorrect],
        ];
    }

    public function attributes()
    {
        return [
            'password' => trans('user.validaton_password'),
        ];
    }
}
