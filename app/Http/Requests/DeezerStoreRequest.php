<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeezerStoreRequest extends FormRequest
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
            'feed_id' => 'required|exists:App\Models\Feed,feed_id,username,' . auth()->user()->username,
            'coo' => 'required|exists:countries,iso_3166_2',
        ];
    }
}
