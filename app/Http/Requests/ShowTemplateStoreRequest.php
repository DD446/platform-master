<?php

namespace App\Http\Requests;

use App\Models\Show;
use Illuminate\Foundation\Http\FormRequest;

class ShowTemplateStoreRequest extends FormRequest
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
            'name' => ['required', 'max:120'],
            'feed_id' => ['nullable', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
            'title' => ['nullable', 'max:255'],
            'description' => ['nullable'],
            'author' => ['nullable', 'max:255'],
            'copyright' => ['nullable', 'max:255'],
            'link' => ['nullable', 'url'],
            'itunes_title' => ['nullable', 'max:255'],
            'itunes_subtitle' => ['nullable', 'max:255'],
            'itunes_summary' => ['nullable'],
            'itunes_episode_type' => ['nullable', 'in:full,episode,trailer'],
            'itunes_season' => ['nullable', 'numeric'],
            'itunes_explicit' => ['nullable'],
            'itunes_logo' => ['nullable', 'numeric'],
            'is_public' => ['nullable', 'in:' . Show::PUBLISH_PAST . ',' . Show::PUBLISH_DRAFT . ',' . Show::PUBLISH_FUTURE . ',' . Show::PUBLISH_NOW]
        ];
    }
}
