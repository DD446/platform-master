<?php

namespace App\Http\Requests;

use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->check()) {
            return false;
        }

        return has_package_feature(auth()->user()->package, Package::FEATURE_MEMBERS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required',
        ];
    }

    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Name des Teams.', # I18N
                'example' => 'redakteure',
            ],
        ];
    }
}
