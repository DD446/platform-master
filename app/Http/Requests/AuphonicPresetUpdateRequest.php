<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuphonicPresetUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'string'],
            'feed_id' => 'required|exists:App\Models\Feed,feed_id,username,' . auth()->user()->username,
        ];
    }
}
