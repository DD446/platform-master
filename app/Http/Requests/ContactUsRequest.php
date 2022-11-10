<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'enquiry_type' => 'required|in:general,commercial,interview,feedback,feature,support,bug,bill',
        ];

        if (!auth()->check()) {
            //array_push($rules, ['g-recaptcha-response' => [new GoogleReCaptchaValidationRule('contact_us')]]);
            //array_push($rules, ['g-recaptcha-response' => 'required|recaptcha:contactus']);
            //array_push($rules, ['g-recaptcha-response' => 'required|captcha']);
            //$rules['mathcaptcha'] = 'required|mathcaptcha';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => trans('contact_us.name'),
            'email' => trans('contact_us.email'),
            'comment' => trans('contact_us.comment'),
            'enquiry_type' => trans('contact_us.enquiry_type'),
        ];
    }
}
