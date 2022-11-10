<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuphonicPresetStoreRequest extends FormRequest
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
            'feed_id' => ['required', 'string', 'max:100', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
            'preset' => ['required', 'string'],
            'title' => ['required', 'string'],
            'summary' => ['nullable', 'string'],
            'subtitle' => ['nullable', 'string'],
            'track' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'artist' => ['nullable', 'string'],
            'publisher' => ['nullable', 'string'],
            'album' => ['nullable', 'string'],
            'url' => ['nullable', 'url'],
            'license' => ['nullable', 'string'],
            'license_url' => ['nullable', 'url'],
            'genre' => ['nullable', 'string'],
            'year' => ['nullable', 'string'],
            'append_chapters' => ['nullable', 'boolean'],
            'location' => ['nullable', 'array'],
            'chapters' => ['nullable', 'array'],
            'image' => ['nullable', 'image'],
        ];
    }
}
