<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
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
     * @bodyParam feed_id string ID des Podcast(-Feed)s. Example: beispiel
     * @bodyParam guid string GUID der Episode. Example: pod-123145689
     *
     * @return array
     */
    public function rules()
    {
        return [
            'feed_id' => 'required',
            'guid' => 'required',
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'copyright' => 'nullable|max:255',
            'is_public' => 'required|in:-1,0,1,2',
            'description' => 'required|max:4000',
            'itunes.title' => 'nullable|max:255',
            'itunes.subtitle' => 'nullable|max:255',
            'itunes.summary' => 'nullable|max:4000',
            'itunes.episode' => 'nullable|numeric',
            'itunes.episodeType' => 'required|in:full,trailer,bonus',
            'itunes.season' => 'nullable|numeric',
            'itunes.logo' => 'nullable|numeric',
            'itunes.duration' => 'nullable',
            'itunes.explicit' => 'nullable|in:yes,true,false,0,1',
            'itunes.isclosedcaptioned' => 'nullable|in:yes',
            'itunes.author' => 'nullable|max:255',
            'publishing_date' => 'required|date',
            'publishing_time' => 'required',
            'link' => 'nullable|url',
            'show_media' => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        $attributes = [
            'title' => trans('shows.validation_attribute_title'),
            'author' => trans('shows.validation_attribute_author'),
            'publishing_date' => trans('shows.validation_attribute_publishingDate'),
            'publishing_time' => trans('shows.validation_attribute_publishingTime'),
        ];

        return $attributes;
    }

    /**
     * @TODO: I18N
     */
    public function bodyParameters()
    {
        return [
            'title' => [
                'description' => 'Titel der Episode',
                'example' => 'Dies ist eine Episode.',
            ],
            'description' => [
                'description' => 'Beschreibung der Episde',
                'example' => '',
            ],
            'author' => [
                'description' => 'Autor der Episode',
                'example' => 'Maxima Musterfrau',
            ],
            'copyright' => [
                'description' => 'Angabe zu Nutzerrechten',
                'example' => 'Podcast-Team MM',
            ],
            'is_public' => [
                'description' => 'Veröffentlichungsstatus',
                'example' => '1',
            ],
            'itunes.title' => [
                'description' => 'Itunes-spezifischer Titel der Episode',
                'example' => '',
            ],
            'itunes.subtitle' => [
                'description' => 'Itunes-spezifischer Untertitel der Episode',
                'example' => '',
            ],
            'itunes.summary' => [
                'description' => 'Itunes-spezifische Zusammenfassung der Episode (ohne HTML)',
                'example' => '',
            ],
            'itunes.episode' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.episodeType' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.season' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.logo' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.duration' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.explicit' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.isclosedcaptioned' => [
                'description' => '',
                'example' => '',
            ],
            'itunes.author' => [
                'description' => '',
                'example' => '',
            ],
            'publishing_date' => [
                'description' => '',
                'example' => '2021-08-26',
            ],
            'publishing_time' => [
                'description' => '',
                'example' => '12:10:59',
            ],
            'link' => [
                'description' => '',
                'example' => '',
            ],
            'show_media' => [
                'description' => 'ID einer Medien-Datei.',
                'example' => '1554927456',
            ],
         ];
    }
}
