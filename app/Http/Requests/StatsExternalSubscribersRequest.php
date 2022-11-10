<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatsExternalSubscribersRequest extends FormRequest
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
            'date_from' => 'nullable|date_format:Y-m-d|after:2007-01-01',
            'date_to' => 'nullable|date_format:Y-m-d|after:2007-01-01',
            //'time_resolution' => 'nullable|in:day,week,month,year',
            'feed_id' => 'nullable|array',
            //'feed_id.*' => 'nullable|exists:App\Models\Feed,feed_id|required_if:source,feed|required_with:guid',
            'source' => ['nullable', Rule::in(['podcaster.de', 'podcast.de', 'overcast', 'breaker', 'feedbin', 'g2reader', 'bloglovin', 'instacast', 'newsify', 'ucast', 'playerfm'])],
        ];
    }

    /**
     * @TODO: I18N
     * @return \string[][]
     */
    public function bodyParameters()
    {
        return [
            'date_from' => [
                'description' => 'Start-Zeitpunkt für die Anzeige der Daten. Format: Jahr-Monat-Tag ("Y-m-d"). Falls leer gelassen oder ungültig, wird als Zeitpunkt heute vor 30 Tagen genommen.',
                'example' => '2020-09-31',
            ],
            'date_to' => [
                'description' => 'End-Zeitpunkt für die Anzeige der Daten. End-Zeitpunkt muss nach dem Start-Zeitpunkt liegen. Format: Jahr-Monat-Tag ("Y-m-d"). Falls leer gelassen oder ungültig, wird das aktuelle Datum ("heute") verwendet.',
                'example' => '2020-10-31',
            ],
/*            'time_resolution' => [
                'description' => 'Gruppierung nach Zeiträumen. Zulässig sind "day", "week", "quarter", "month", "year". "quarter" ist nur im Paket "Corporate" verfügbar. Standard ist "day".',
                'example' => 'day',
            ],*/
            'feed_id' => [
                'description' => 'Liste von IDs von Podcast(-Feed)s. Die Feed-IDs lassen sich über Podcasts > List Podcasts abrufen. Falls leer gelassen, werden alle Podcasts berücksichtigt.',
                'example' => '["beispiel"]',
            ],
            'feed_id.*' => [
                'description' => 'ID eines Podcast(-Feed)s. Wert muss als "Array" übergeben werden. ',
                'example' => 'beispiel',
            ],
            'source' => [
                'description' => 'Filter für Dienst.',
                'example' => 'podcast.de',
            ]
        ];
    }
}
