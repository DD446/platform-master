<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FundsAvailableRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $funds = auth()->user()->funds;

        return $funds >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $funds = auth()->user()->funds;

        return trans('package.message_error_funds_too_low',  ['currency' => 'EUR' /** TODO: I18N */, 'funds' => number_format($funds, 2), 'uri' => route('accounting.create')]);
    }
}
