<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Service
    |--------------------------------------------------------------------------
    | Type: bool
    |
    | This option is used to disable/enable the service
    |
    | Supported: true, false
    |
    */
    'is_service_enabled' => true,
    /*
    |--------------------------------------------------------------------------
    | Host Name
    |--------------------------------------------------------------------------
    | Type: string
    | Default will be empty, assign value only if you want domain check with Google response
    | Google reCAPTCHA host name, https://www.google.com/recaptcha/admin
    |
    */
    'host_name' => '',
    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    | Type: string
    | Google reCAPTCHA credentials, https://www.google.com/recaptcha/admin
    |
    */
    'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
    /*
    |--------------------------------------------------------------------------
    | Site Key
    |--------------------------------------------------------------------------
    | Type: string
    | Google reCAPTCHA credentials, https://www.google.com/recaptcha/admin
    |
    */
    'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Badge Style
    |--------------------------------------------------------------------------
    | Type: string
    | Support:
    |  -  true: the badge will be shown inline with the form, also you can customise your style
    |  -  false: the badge will be shown in the bottom right side
    |
    */
    'inline' => true,

    /*
    |--------------------------------------------------------------------------
    | Score Comparision
    |--------------------------------------------------------------------------
    | Type: bool
    | If you enable it, the package will do score comparision from your setting
    */
    'is_score_enabled' => false,
    /*
    |--------------------------------------------------------------------------
    | Setting
    |--------------------------------------------------------------------------
    | Type: array
    | Define your score threshold, define your action
    | action: Google reCAPTCHA required parameter
    | threshold: score threshold
    | score_comparision: true/false, if this is true, the system will do score comparsion against your threshold for the action
    */
    'setting' => [
        [
            'action' => 'contact_us',
            'threshold' => 0.7,
            'score_comparision' => true
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    | Custom option field for your request setting, which will be used for RequestClientInterface
    |
    */
    'options' => [

    ],
    /*
    |--------------------------------------------------------------------------
    | Site Verify Url
    |--------------------------------------------------------------------------
    | Type: string
    | Google reCAPTCHA API
    */
    'site_verify_url' => 'https://www.google.com/recaptcha/api/siteverify',

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    | Type: string
    | https://developers.google.com/recaptcha/docs/language
    */
    'language' => 'de'
];
