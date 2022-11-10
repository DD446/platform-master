<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatsSubscribersRequest extends FormRequest
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
        $username = auth()->user()->username;

        return [
            'df' => 'nullable|date|after:2007-01-01',
            'dt' => 'nullable|date|after:2007-01-02',
            'time_resolution' => 'nullable|in:day,week,quarter,month,year',
            'feed_id' => ['nullable', 'string', 'exists:App\Models\Feed,feed_id,username,' . $username],
            //'feed_id.*' => 'nullable|exists:App\Models\Feed,feed_id|required_if:source,feed|required_with:guid',
            'user_agent' => 'nullable|array',
            //'user_agent.*' => 'min:3',
            'user_agent_type' => 'nullable|array',
            //'user_agent_type.*' => 'nullable|in:bots,desktop,apps,browsers,unknown',
            'os' => 'nullable|array',
            //'os.*' => ['nullable', Rule::in(['iOS', 'BlackBerry', 'BlackBerry', 'Windows Phone', 'Windows Phone', 'Android', 'Windows 3.11', 'Windows 95', 'Windows 98', 'Windows 2000', 'Windows XP', 'Windows Server 2003', 'Windows Vista', 'Windows 7', 'Windows 8', 'Windows 10',  'Windows NT 4.0', 'Windows ME', 'Open BSD', 'Sun OS', 'Linux', 'Mac OS', 'QNX', 'BeOS', 'OS/2'])],
            'country' => 'nullable|array',
            //'country.*' => 'nullable|min:2|max:3',
            'referer' => 'nullable|url',
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
            'time_resolution' => [
                'description' => 'Gruppierung nach Zeiträumen. "quarter" nur in Paket "Corporate" verfügbar. Standard ist "day".',
                'example' => 'day',
            ],
            'feed_id' => [
                'description' => 'ID eines Podcast(-Feed)s. Wird vorausgesetzt, wenn der Parameter "source" den Wert "feed" hat. Die Feed-IDs lassen sich über Podcasts > List Podcasts abrufen.',
                'example' => '["beispiel"]',
            ],
            'feed_id.*' => [
                'description' => 'ID eines Podcast(-Feed)s. Wert muss als "Array" übergeben werden. ',
                'example' => 'beispiel',
            ],
            'user_agent' => [
                'description' => 'Liste von User-Agents. Falls leer gelassen, werden alle User-Agents berücksichtigt.',
                'example' => '["Mozilla (compatible)", "Chrome"]',
            ],
            'user_agent.*' => [
                'description' => 'Kennung eines User-Agents. Wert muss als "Array" übergeben werden.',
                'example' => 'Mozilla (compatible)',
            ],
            'user_agent_type' => [
                'description' => 'Liste von Klassifizierungen von User-Agents. Falls leer gelassen, werden die Typen "desktop", "apps", "browsers" berücksichtigt.',
                'example' => '["desktop", "apps", "browsers"]',
            ],
            'user_agent_type.*' => [
                'description' => 'User-Agent-Typ. Gültige Werte sind: "desktop", "apps", "browsers", "bots", "unknown". Wert muss als "Array" übergeben werden.',
                'example' => 'desktop',
            ],
            'os' => [
                'description' => 'Liste von Betriebssystemen. Falls leer gelassen, werden alle Betriebssysteme berücksichtigt.',
                'example' => '["iOS"]',
            ],
            'os.*' => [
                'description' => 'Klassifizierung des Betriebssystemes. Gültige Werte sind: "iOS", "BlackBerry", "BlackBerry", "Windows Phone", "Windows Phone", "Android", "Windows 3.11", "Windows 95", "Windows 98", "Windows 2000", "Windows XP", "Windows Server 2003", "Windows Vista", "Windows 7", "Windows 8", "Windows 10",  "Windows NT 4.0", "Windows ME", "Open BSD", "Sun OS", "Linux", "Mac OS", "QNX", "BeOS", "OS/2". Wert muss als "Array" übergeben werden.',
                'example' => 'iOS',
            ],
            'country' => [
                'description' => 'Liste von Länder-/Regions-Code. Falls leer gelassen, werden alle Länder berücksichtigt.',
                'example' => '["DEU", "AUT"]'
            ],
            'country.*' => [
                'description' => '3-stelliger Länder-/Regions-Code (in einiger Ausnahmen auch 2-stellig, z.B. EU für Europa). Wert muss als "Array" übergeben werden.',
                'example' => 'DEU'
            ],
            'referer' => [
                'description' => 'Verweis-Link auf die Quelle. Wert muss eine gültige URL sein. Falls leer gelassen, wird das Ergebnis nicht eingeschränkt.',
                'example' => '["https://www.podcast.de/"]'
            ]
        ];
    }
}
