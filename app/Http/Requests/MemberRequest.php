<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'email' => 'required|email',
        ];
    }

    public function bodyParameters()
    {
        return [
            'team_id' => [
                'description' => 'ID eines Teams.', # I18N
                'example' => '4',
            ],
            'email' => [
                'description' => 'E-Mail-Adresse', # I18N
                'example' => 'user@example.org',
            ],
        ];
    }
}
