@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_podcasthost')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_podcasthost', ['service' => config('app.name')])}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12 col-md-12">

                    <p>
                        <a href="https://jam.podcaster.de/" title="Der Podcast-Jam ist Kunde unseres Podcast Host Angebotes."><img src="/images/Content/site/podcast-host_300.png" alt="Podcast-Host für den Podcast-Jam" class="img img-fluid rounded float-right pl-0 pl-md-4 pb-2" /></a> Du möchtest Deinen eigenen Podcast veröffentlichen, aber weißt nicht, wie Du das anstellen sollst? Außerdem möchtest Du Dich nicht mit technischen Details eines Podcast-Hosts auseinandersetzen, sondern einfach nur Podcasten? Bei uns findet jeder <strong>das optimale Podcast Host-Paket</strong>: <a href="/pakete">von Anfänger bis Profi</a>. Mit unseren vier Paketen bieten wir Dir maßgeschneiderte Lösungen zum Podcasten. Wir sind Dein kompetenter Podcast Host!
                    </p>

                    <h3>{{ config('app.name') }} als Podcast-Host</h3>

                    <p>
                        Unter den Angeboten Starter, Podcaster, Profi und Vodcaster findest du genau das Richtige für deine individuellen Bedürfnisse. Wenn du neu im Podcasting bist und es zuerst einmal nur ausprobieren möchtest, dann bist du bei unserem <strong>Angebot Starter</strong> genau richtig. Für <strong>nur 1 Euro im Monat</strong> erhältst du jeden Monat 50 Megabyte Speicherplatz.  Das Starter-Paket reicht dir nicht zu einem späteren Zeitpunkt nicht aus oder du benötigst von Anfang an einen größeren Leistungsumfang? Mit unserem Podcaster-Angebot bekommst du für nur 5 Euro im Monat 250 Megabyte Speicherplatz. Profis erhalten bei uns im Profi-Angebot Speicherplatz für rund 10 Stunden Audio monatlich für günstige 10 Euro monatlich.
                    </p>

                    <h3>Podcast-Host mit großem Funktionsumfang</h3>

                    <p>
                        Statistiken, Webspace und ein Blog sind auch in allen anderen Angeboten enthalten. Dein Blog ist dank HTML5 auf neuestem Stand. Dir stehen dazu viele Möglichkeiten zur Verfügung, das Aussehen einfach nach deinen Wünschen anzupassen. <strong>WordPress</strong> mit seinen Widgets und Plugins macht es möglich. Auch ohne HTML5-Kenntnisse kannst du deine Seite individualisieren.
                    </p>

                    <h3>Flexibilität schreiben wir groß</h3>

                    <p>
                        Audio reicht dir nicht? Du möchtest zusätzlich zu MP3 auch Video Podcasting betreiben? Mit unserem Vodcaster-Angebot neben Audio auch Videoinhalte bereitstellen! Nur 20 Euro im Monat und du erhältst für den Podcast Host 2 Gigabyte an Speicherplatz für bis zu 5 Podcasts. Die Anzahl an möglichen Podcasts variiert von Paket zu Paket. Selbstverständlich kannst Du Deine Podcasts auch <strong>mit iTunes und Spotify verknüpfen</strong>. Über iTunes und Spotify erreichst Du potentiell Millionen Hörer. Mit unserem Player kannst Du ganz einfach eine Webseite aufwerten. Deine eigene <em>Karriere im Podcasting</em> starten, war noch nie so einfach. Bist Du Power-User und der Speicherplatz für Audios oder Videos reicht in einem Monat mal nicht aus, kannst Du jederzeit einmalig oder monatlich wiederkehrend Deinen den Speicherplatz Deines Paketes aufstocken.s
                    </p>

                    <h3>Service beim Podcasting</h3>

                    <p>
                        Egal wieviele Podcasts Deine Hörer herunterladen, die Kosten bleiben immer gleich. <strong>Unbegrenzte Downloads Deiner Podcasts</strong> ist in jedem Paket enthalten. So musst Du Dir keine Gedanken über verbrauchtes Übertragungsvolumen machen. Außerdem bekommst Du monatlich neuen Speicherplatz zur Verfügung gestellt. Im Vergleich findest Du das passende Angebot bei uns. Vergleiche so oft du willst! Unsere Angebote sind preiswert und bieten Dir sehr viel Leistung und alle Funktionen, die Du von einem Podcast-Host erwartest.
                    </p>

                    <h3>Rechenzentrum in Deiner Nähe</h3>

                    <p>
                        Unser Server-Standort in Deutschland bietet nicht nur uns als <em>Podcast Host</em> und Dir als Podcaster einen Vorteil, sondern die geographische Nähe ermöglicht schnelle Zugriffszeiten für Deine Nutzer. So können die Podcasts nicht nur schnell heruntergeladen werden, sondern auch wie ein Stream direkt abgespielt werden.
                    </p>

                    <div class="pt-5 row justify-content-center">
                        <a href="{{ route('packages') }}" class="btn btn-primary btn-lg">Wähle jetzt Dein Paket aus</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection('content')
