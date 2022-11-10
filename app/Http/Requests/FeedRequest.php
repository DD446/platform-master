<?php

namespace App\Http\Requests;

use App\Classes\Datacenter;
use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
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
        $rules = [
            'feed_id' => 'required|string|max:100|exists:App\Models\Feed,feed_id,username,' . auth()->user()->username,
            'rss.title' => 'required|max:255',
            'rss.description' => 'required|max:4000',
            'rss.link' => 'nullable|url|active_url',
            'rss.email' => 'required|email',
            'rss.author' => 'required|max:255',
            'rss.copyright' => 'required|max:255',
            'rss.language' => 'required|max:3|exists:languages,id',
            'rss.category' => 'nullable|max:255',
            'itunes.title' => 'nullable|max:255',
            'itunes.subtitle' => 'nullable|max:255',
            'itunes.summary' => 'nullable|max:4000',
            'itunes.logo' => 'nullable|numeric',
            'itunes.explicit' => 'nullable|in:yes,no,true,false',
            'itunes.block' => 'nullable|in:yes,no',
            'itunes.complete' => 'nullable|in:yes,no',
            'itunes.author' => 'nullable|max:255',
            'itunes.type' => 'in:episodic,serial',
            'itunes.new-feed-url' => 'nullable|active_url|max:255',
            'itunes.category' => 'required|array|min:1|max:3|in:' . implode(',', array_keys(Datacenter::getItunesCategories())),
            "itunes.category.*"  => "required|string|distinct|min:1",
            'googleplay.description' => 'nullable|max:4000',
            'googleplay.category' => ['nullable', 'in:' . implode(',', array_keys(Datacenter::getGooglePlayCategories()))],
            'googleplay.block' => 'nullable|in:yes,no',
            'googleplay.explicit' => 'nullable|in:yes,no',
            // TODO: googleplay:image
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'feed_id' => trans('feeds.validation_attribute_feed_id'),
            'itunes.category.0' => trans('feeds.validation_attribute_itunes_category_0'),
            'itunes.category.1' => trans('feeds.validation_attribute__itunes_category_1'),
            'itunes.category.2' => trans('feeds.validation_attribute__itunes_category_2'),
            'itunes.explicit' => trans('feeds.validation_attribute_itunes_explicit'),
            'googleplay.category' => trans('feeds.validation_attribute_google_category'),
            'googleplay.explicit' => trans('feeds.validation_attribute_google_explicit'),
        ];
    }

    public function messages()
    {
        return [
            'itunes.category.1' => trans('package.validation_error_id_required'),
        ];
    }


    /**
     * @TODO: I18N
     */
    public function bodyParameters()
    {
        $params = [
            'title' => [
                'description' => 'Titel des Podcasts',
                'example' => 'Dies ist ein Podcast.',
            ],
            'description' => [
                'description' => 'Beschreibung des Podcasts',
                'example' => 'Diese Episode ist über das podcaster API.',
            ],
            'author' => [
                'description' => 'Autor des Podcasts',
                'example' => 'Fabio',
            ],
            'copyright' => [
                'description' => 'Angabe zu Nutzerrechten',
                'example' => 'Free to use',
            ],
            'link' => [
                'description' => 'Link zur Webseite des Podcasts',
                'example' => 'https://beispiel.podcast.de',
            ],
            'itunes.subtitle' => [
                'description' => 'Untertitel',
                'example' => 'Das ist der Untertitel',
            ],
            'itunes.summary' => [
                'description' => 'Zusammenfassung',
                'example' => 'Beschreibung der Episode ohne HTML',
            ],
            'itunes.logo' => [
                'description' => 'ID einer Mediendatei (Logo)',
                'example' => '123456789',
            ],
            'itunes.explicit' => [
                'description' => 'Inhalt nur für Erwachsene geeignet',
                'example' => '',
            ],
            'itunes.type' => [
                'description' => 'Podcast-Typ',
                'example' => 'episodic',
            ],
            'itunes.block' => [
                'description' => 'Abruf blockieren',
                'example' => 'no',
            ],
            'itunes.complete' => [
                'description' => 'Beendet/Vollständig',
                'example' => 'no',
            ],
            'googleplay.description' => [
                'description' => 'Beschreibung des Podcasts (Google)',
                'example' => 'Diese Episode ist über das podcaster API erstellt.',
            ],
            'googleplay.category' => [
                'description' => 'Kategorie des Podcasts (Google)',
                'example' => 'Arts',
            ],
            'googleplay.complete' => [
                'description' => 'Beendet/Vollständig (Google)',
                'example' => 'no',
            ],
            'googleplay.block' => [
                'description' => 'Abruf blockieren (Google)',
                'example' => 'no',
            ],
        ];

        return $params;
    }
}
