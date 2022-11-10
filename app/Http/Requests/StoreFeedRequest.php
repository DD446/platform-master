<?php

namespace App\Http\Requests;

use App\Classes\Datacenter;
use App\Rules\IsPodcastFeed;
use App\Rules\UniqueFeedId;
use App\Rules\UniqueSubdomain;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() || app()->runningInConsole();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (app()->runningInConsole()) {
            $username = request('username');
        } else {
            $username = auth()->user()->username;
        }

        return [
            'feed_id' => ['required', 'string', 'max:100', new UniqueFeedId($username)],
            'feed_url' => ['nullable', 'string', 'url', 'active_url', new IsPodcastFeed],
            'rss.title' => 'required_without:feed_url|max:255',
            'rss.author' => 'required_without:feed_url|max:255',
            'rss.description' => 'required_without:feed_url|max:4000',
            'rss.copyright' => 'required_without:feed_url|max:255',
            'rss.link' => 'nullable|url|active_url',
            'rss.email' => 'required_without:feed_url|email',
            'rss.language' => 'required_without:feed_url|max:3|exists:languages,id',
            'rss.category' => 'nullable|max:255',
            'itunes.title' => 'nullable|max:255',
            'itunes.subtitle' => 'nullable|max:255',
            'itunes.summary' => 'nullable|max:4000',
            //'itunes.logo' => 'nullable|numeric',
            'itunes.explicit' => 'nullable|in:no,yes,true,false',
            'itunes.block' => 'nullable|in:no,yes,true,false',
            'itunes.complete' => 'nullable|in:no,yes,true,false',
            'itunes.author' => 'nullable|max:255',
            'itunes.category' => 'required_without:feed_url|array|min:1|max:3',
//            'itunes.category' => 'required_without:feed_url|array|min:1|max:3|in:' . implode(',', array_keys(Datacenter::getItunesCategories())),
//            "itunes.category.*"  => "required_without:feed_url|string|distinct|min:1",
            'googleplay.description' => 'nullable|max:4000',
            'googleplay.category' => ['nullable', 'in:' . implode(',', array_keys(Datacenter::getGooglePlayCategories()))],
            'googleplay.block' => 'nullable|in:yes,no',
            'googleplay.explicit' => 'nullable|in:yes,no',
            'domain.protocol' => 'nullable|string|in:http,https',
            'domain.domain' => 'nullable|string|max:100',
            'domain.subdomain' => ['required_without:feed_url', 'string', 'max:100', new UniqueSubdomain($username), 'regex:/^([A-Za-z0-9](?:[A-Za-z0-9\.\-]{0,61}[A-Za-z0-9])?$)/u'],
            'logo.itunes' => 'nullable|digits:10'
            //'logo_url' => ['image', 'url', 'active_url', 'mimes:jpeg,png', 'nullable', 'max:1512', 'dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1.0', new LogoHasNoTransparency],
        ];
    }

    public function attributes()
    {
        return [
            'feed_id' => trans('feeds.validation_attribute_feed_id'),
            'feed_url' => trans('feeds.validation_attribute_feed_url'),
            'itunes.category' => trans('feeds.validation_attribute_itunes_category'),
            'itunes.category.0' => trans('feeds.validation_attribute_itunes_category_0'),
            'itunes.category.1' => trans('feeds.validation_attribute_itunes_category_1'),
            'itunes.category.2' => trans('feeds.validation_attribute_itunes_category_2'),
            'rss.link' => trans('feeds.validation_attribute_rss_link'),
        ];
    }

    /**
     * @TODO: I18N
     */
    public function bodyParameters()
    {
        return [
            'feed_id' => [
                'description' => 'ID (Bezeichner) für den Podcast(-Feed)',
                'example' => 'neuer-podcast',
            ],
            'rss.title' => [
                'description' => 'Titel des Podcasts',
                'example' => 'Dies ist ein Podcast.',
            ],
            'rss.description' => [
                'description' => 'Beschreibung des Podcasts',
                'example' => 'Diese Episode ist über das podcaster API.',
            ],
            'rss.author' => [
                'description' => 'Autor des Podcasts',
                'example' => 'Fabio',
            ],
            'rss.copyright' => [
                'description' => 'Angabe zu Nutzerrechten',
                'example' => 'Free to use',
            ],
            'rss.link' => [
                'description' => 'Link zur Webseite des Podcasts',
                'example' => 'https://beispiel.podcast.de',
            ],
            'itunes.subtitle' => [
                'description' => 'Untertitel',
                'example' => 'Das ist der Untertitel',
            ],
            'itunes.explicit' => [
                'description' => 'Inhalt nur für Erwachsene geeignet',
                'example' => '',
            ],
            'itunes.type' => [
                'description' => 'Podcast-Typ',
                'example' => 'episodic',
            ],
            'itunes.complete' => [
                'description' => 'Beendet/Vollständig ',
                'example' => 'yes',
            ],
            'logo' => [
                'description' => 'ID einer Mediendatei (Logo)',
                'example' => '123456789',
            ]
        ];
    }
}
