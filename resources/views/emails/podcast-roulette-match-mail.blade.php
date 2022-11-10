@component('mail::message')
# Liebe Podcast-Freunde,

vielen Dank für eure zahlreichen Anmeldungen zur zweiten Runde des <a href="https://www.podcast.de/podcast/1665559/podcast-roulette" target="_blank">Podcast Roulettes</a>. Wir freuen uns riesig auf die spannenden Begegnungen und unterhaltsamen Episoden. Die Teilnehmerzahl hat sich im Vergleich zur letzten Runde übrigens verdoppelt.

## Ablauf
Ihr habt euch angemeldet und wurdet nun mit euren Roulette-Partnern zusammengelost. Nun organisiert ihr die gemeinsame Aufnahme einer Podcast-Episode.  Ladet eure Episode bitte **bis zum 30. November 2021** über <a href="https://www.podcaster.de/roulette" target="_blank">unsere Roulette-Seite</a> hoch, damit wir sie im Podcast Roulette einplanen können. Parallel dazu dürft ihr die Episode natürlich auch auf euren eigenen Podcast-Kanälen veröffentlichen.

## Thema und Partner
Das Thema des Podcast Roulettes lautet diesmal: **Die größten Jugendsünden**. Bitte fühlt euch dazu ermutigt, das Thema so weit zu fassen wie ihr mögt und nach Belieben abzuschweifen.
Als Podcast Roulette-Partner wurde(n) euch zugelost: `{{ $partner }}` vom Podcast <a href="https://www.podcast.de/suche/?q={{ $podcastTitle }}" target="_blank">`{{ $podcastTitle }}`</a>.

@component('mail::panel')
    Zur Terminabsprache sendet eine Mail an: <a href="mailto:{{ $email }}?subject=Terminabsprache Podcast-Roulette">`{{ $email }}`</a>
@endcomponent

## Material

Für den Anfang und das Ende der Episode könnt ihr <a href="https://bzjdis.podcaster.de/download/podcast-roulette_jingle-gesprochen.mp3" target="_top">diesen Jingle</a> benutzen. <a href="https://bzjdis.podcaster.de/download/podcast-roulette-logo-1500px-quadratisch.jpg" target="_top">Hier findet ihr das Coverbild.</a> Fühlt euch frei, eure gemeinsame Episode nach Belieben mit Shownotes zu versehen.

## Social Media

Begleitet das Podcast Roulette gerne mit dem Hashtag **#PodcastRoulette** auf Social Media und markiert uns in euren Beiträgen. Ihr findet uns auf
<a href="https://fb.com/podcast.hosting" target="_blank">Facebook</a> – <a href="https://www.linkedin.com/company/podcasthosting/" target="_blank">LinkedIn</a> –
<a href="https://twitter.com/podcasterDE" target="_blank">Twitter</a> – <a href="https://instagram.com/podcastplattform" target="_blank">Instagram</a>.

## Abonnieren und nichts verpassen

Abonniert das Podcast Roulette gerne auf <a href="https://www.podcast.de/podcast/1665559/podcast-roulette" target="_blank">podcast.de</a>,
<a href="https://open.spotify.com/show/3iaKxuiBqSjjGZrAFsVeHI" target="_blank">Spotify</a>, <a href="https://podcasts.apple.com/us/podcast/podcast-roulette/id1566851710?uo=4" target="_blank">Apple Podcasts</a>, <a href="https://www.deezer.com/de/show/2555642" target="_blank">Deezer</a> oder <a href="https://bzjdis.podcaster.de/PodcastRoulette.rss" target="_blank">per RSS</a> oder überall sonst, wo ihr eure Podcasts hört. So geht ihr sicher, dass ihr keine Folge verpasst.

## Rückfragen

Bei Fragen oder Problemen wendet euch gerne jederzeit an <a href="mailto:steffen+podcastroulette@podcaster.de">steffen+podcastroulette@podcaster.de</a>

Habt viel Spaß bei euren Aufzeichnungen Wir freuen uns riesig auf die Ergebnisse!

Herbstliche Grüße,<br>
Euer podcast.de Team
@endcomponent
