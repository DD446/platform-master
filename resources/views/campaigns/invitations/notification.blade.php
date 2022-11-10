@component('mail::message')
# Anfrage für eine Kampagne

Hallo Podcaster!

Es liegt eine neue Anfrage für Werbung in Deinem Podcast "{{ $feed->rss['title'] }}" vor.

{{ $campaign->description }}

Um weitere Informationen zur Kampagne zu erhalten, antworte einfach auf diese Mail.

Viele Grüße,<br>
{{ $campaign->name }}

<small>Du erhältst diese Mail, weil Du Interesse an Werbung in Deinem Podcast signalisiert hast. Du kannst den Empfang dieser Angebote in den <a href="{{ config('app.url') }}/podcast/{{ $feed->feed_id }}/">Einstellungen zu Deinem Podcast</a> abschalten.</small>
@endcomponent
