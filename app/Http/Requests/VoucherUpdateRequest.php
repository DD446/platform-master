<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherUpdateRequest extends FormRequest
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
            'voucher' => [
                'required',
                'string',
                Rule::exists('vouchers', 'voucher_code')->where(function ($query) {
                    $query
                        ->where('amount', '<>', 0)
                        ->where(function($query) {
                            $query->whereNull('valid_until')
                                ->orWhereDate('valid_until', '>=', now());
                        })
                        ->where(function($query) {
                            $query->where('valid_for', '=', -1)
                                ->orWhere('valid_for', '=', auth()->id());
                        });
                })
            ],
        ];
    }

    public function attributes()
    {
        return [
            'voucher' => trans('accounting.label_voucher'),
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['voucher'] = $this->route('voucher');

        return $data;
    }
}
