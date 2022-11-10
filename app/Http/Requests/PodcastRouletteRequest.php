<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PodcastRouletteRequest extends FormRequest
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
            'email' => ['email', 'required'],
            'feed_id' => ['string', 'required', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
            'podcasters' => ['string', 'required', 'min:3'],
            'first_time' => ['bool'],
        ];
    }
}
