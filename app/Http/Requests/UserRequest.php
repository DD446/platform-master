<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'name_title' => ['nullable', 'min:2'],
            'first_name' => ['nullable', 'min:2'],
            'last_name' => ['required', 'min:2'],
            'telephone' => ['nullable', 'min:2'],
            'telefax' => ['nullable', 'min:2'],
            'url' => ['nullable', 'min:6', 'url'],
            'organisation' => ['nullable'],
            'department' => ['nullable'],
            'street' => ['nullable', 'min:3'],
            'housenumber' => ['nullable'],
            'city' => ['nullable'],
            'country' => [
                'required',
                Rule::in(array_keys($countries)),
            ],
            'post_code' => ['nullable', 'min:2'],
            'representative' => ['nullable', 'min:2'],
            'mediarepresentative' => ['nullable', 'min:2'],
            'register_court' => ['nullable', 'min:2'],
            'register_number' => ['nullable', 'min:2'],
            'board' => ['nullable', 'min:2'],
            'chairman' => ['nullable', 'min:2'],
            'controlling_authority' => ['nullable', 'min:2'],
            'additional_specifications' => ['nullable'],
        ];
    }

    public function bodyParameters()
    {
        return [
            'name_title' => [
                'description' => trans('Title from profession or heritage'), # I18N
                'example' => 'Prof.',
            ],
            'first_name' => [
                'description' => trans('First name'), # I18N
                'example' => 'Rod',
            ],
            'last_name' => [
                'description' => trans('Surname'), # I18N
                'example' => 'Treslo',
            ],
            'telephone' => [
                'description' => trans('Telephone number'), # I18N
                'example' => '030-549072653',
            ],
            'telefax' => [
                'description' => trans('Fax number'), # I18N
                'example' => '030-549072660',
            ],
            'url' => [
                'description' => trans('Website url'), # I18N
                'example' => 'https://www.podcaster.de',
            ],
        ];
    }
}
