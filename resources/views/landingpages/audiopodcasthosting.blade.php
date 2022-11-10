@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_audiopodcasthosting')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_audiopodcasthosting', ['service' => config('app.name')])}}</span>
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
                        <a href="http://nettesfrettchen.podcaster.de/" target="_blank"><img src="/images/Content/site/audio-podcast-hosting_300.png"
                                alt="Audio Podcast Hosting für das nette Frettchen"
                                class="img img-fluid rounded float-right pl-4 pb-2"/></a> Du suchst einen Host für deine Audiodateien? Du betreibst einen Blog und brauchst fürs Audio Podcast Hosting
                        <span class="font-weight-bold">Webspace für MP3-Dateien</span> oder andere Audioformate? Dann bist du bei podcaster.de goldrichtig.
                    </p>

                    <h3>Audio-Podcast Hosting von {{ config('app.name') }}</h3>

                    <p>
                        Wir bieten genug Webspace für deinen Audio Podcast. <em>Variable Speicherpakete</em> und optional erhältlicher Zusatzspeicher ermöglichen es dir, ein umfangreiches Archiv anzulegen, was für einen Blog unerlässlich ist. <strong>Jeden Monat erhältst du zusätzlichen Webspace.</strong> So bleibt dein Blog individuell erweiterbar. Wir verfügen über große Erfahrung im Bereich Webhosting und bieten dir einen umfangreichen Support. Die Verwendung bewährter Technologien ermöglicht eine hohe Stabilität und Verfügbarkeit der Dateien. Unser eingespieltes Team freut sich darauf, dir eine maßgeschneiderte Lösung anzubieten. Schau dich auf unseren Seiten um und du wirst schnell herausfinden, dass podcaster.de der optimale Host für dich ist.
                    </p>

                    <h3>Viel Service zum kleinen Preis</h3>

                    <p>
                        Unsere Paketangebote fürs Hosting bieten viel Service zum kleinen Preis. Übrigens müssen für deinen <strong>Audio Podcast</strong> bei uns die Dateien nicht zwingend als MP3 hochgeladen werden. Du bist bei der Wahl deines Audioformats flexibel. Oder bitte gleich Podcasts in mehreren Formaten, z.B. neben MP3 noch AAC und OGG an. Du kannst in jedem Paket mehr als einen Podcast-Feed erstellen. Wenn du also ein anderes Audio Format vorziehst, stellt das kein Problem dar. Natürlich bieten wir auch ein Paket fürs <a href="{{ route('lp.videohosting') }}" title="Mehr über unser Video-Hosting">Video Podcast Hosting</a> an. Deiner Kreativität sind beim Podcasten keine Grenzen gesetzt. Damit du dich auch mit deinem Blog kreativ ausleben kannst, stehen dir umfangreiche Einstellmöglichkeiten in deinem persönlichen Blog-Bereich zur Verfügung. Wenn du dich für podcaster.de für dein Hosting entscheidest, hast du einen zuverlässigen Partner an deiner Seite. Wir sind von unserem Service selbst so überzeugt, dass wir unseren Kunden eine <strong>unverbindliche Testphase von 30 Tagen</strong> Dauer anbieten. Und auch nach Ablauf des Versuchsmonats hast du noch weitere volle vier Wochen Zugriff auf deine Audio- oder sonstigen Dateien.
                    </p>

                    <h3>Hohe Verfügbarkeit dank moderner Technik</h3>

                    <p>
                        Wir wissen sehr gut, dass in einer Multimedia-Welt Podcasting hoch im Kurs steht und erfolgreiches Podcasten sowohl von der Aktualität als auch von seiner Verfügbarkeit abhängig ist. Darum setzen wir bewährte Technik aus der Open Source-Welt ein und halten unseren Host <strong>immer auf dem aktuellen Stand</strong>. Nähere Infos zu Fragen rund ums Hosting der Dateien, egal ob es sich nun um MP3 oder irgendein anderes Format handelt, findest du auf unserer Webseite. Wir wünschen viel Spaß beim Stöbern. Und wenn es noch irgendwelche Fragen gibt, hilft dir <a href="{{ route('contactus.create') }}" title="Kontaktiere uns">unser Support</a> gerne weiter.
                    </p>

                    <div class="pt-5 align-content-center">
                        <a href="{{ route('package.order', ['id' => 3, 'name' => trans_choice('package.package_name', 'profi')]) }}" class="btn btn-primary">Werde Profi-Podcaster!</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection('content')
