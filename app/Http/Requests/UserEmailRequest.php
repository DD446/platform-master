<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEmailRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
            ],
            'newemail' => 'required|email|confirmed|unique:usr,email|unique:user_queue,email'
        ];
    }

    public function attributes()
    {
        return [
            'email' => trans('user.attribute_email'),
            'newemail' => trans('user.attribute_newemail'),
        ];
    }
}
