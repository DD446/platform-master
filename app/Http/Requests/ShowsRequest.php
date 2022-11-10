<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowsRequest extends FormRequest
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
        ];
    }

    public function bodyParameters()
    {
        return [
            'feed_id' => [
                'description' => 'ID des Podcast(-Feed)s.',
                'example' => 'beispiel',
            ]
        ];
    }
}
