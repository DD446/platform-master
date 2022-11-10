@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('tour') }}
@endsection

@section('content')

    <section class="space-sm bg-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_tour')}}</h1>
                    <span class="title-decorative">{{trans('pages.lead_tour')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="container">
            <div class="row justify-content-around align-items-center">
                <div class="col-8 col-md-7 col-lg-6 order-md-2 mb-5 mb-md-0">
                    <img alt="Neuen Podcast starten" src="{{ asset('images1/tour/podcast-hosting-einfach-podcasts-veroeffentlichen.jpg') }}" class="w-100" />
                </div>
                <!--end of col-->
                <div class="col-12 col-md-6 col-lg-5 order-md-1 mt-3">
                    <h1 class="display-4">Veröffentliche einfach Podcasts</h1>
                    <span class="lead">
                        Egal, ob Du neu anfängst oder Du mit Deinem Podcast den Host wechselst:
                        Mit wenigen Klicks kannst Du bei uns mit Deinem Kanal starten – <span class="font-weight-bold">einfach und übersichtlich</span>.
                    </span>
                    <div class="mb-4">
                        <span class="text-muted text-small">Alles, was Du für den Start brauchst</span>
                    </div>
                    <a href="{{ route('packages') }}" class="btn btn-primary btn-lg">Lege sofort los</a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="space-md">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h6 class="font-weight-lighter">Referenzkunden</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <ul class="list-inline list-inline-large">
                        <li class="list-inline-item">
                            <img alt="Diakonie" class="logo logo-lg" src="{{ asset('images1/referenzen/diakonie-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Deutscher Jugendherbergsverband" class="logo logo-lg" src="{{ asset('images1/referenzen/djh-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Bundeswehr" class="logo logo-lg" src="{{ asset('images1/referenzen/bundeswehr-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Deutsche Börse" class="logo logo-lg" src="{{ asset('images1/referenzen/deutsche-boerse-gruppe-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Epson" class="logo logo-lg" src="{{ asset('images1/referenzen/epson-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                    </ul>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="bg-white">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Podcast erstellen" src="{{ asset('images1/tour/einfach-neuen-podcast-erstellen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Neuen Podcast starten</h3>
                        <h5>Einfach loslegen</h5>
                        <p>
                            Als <span class="font-weight-bold">Neueinsteiger</span> kannst Du im Handumdrehen <b>einen
                                Podcast erstellen</b>: Lege einen neuen Podcast-Kanal inklusive RSS-Feed, Coverbild, Beschreibung und Kategorien an. Dank der klar strukturierten Schaltflächen findest Du sofort, was Du für den Start brauchst.
                        </p>
<!--                        <a href="#">More about Phasers &rsaquo;</a>-->
                        <a href="{{ route('lp.video', ['video' => 7, 'slug' => 'neuen-podcast-anlegen']) }}?h=1" class="btn btn-link btn-lg" target="_blank" rel="noopener noreferrer" onclick="window.open(this.href, 'video','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"><i class="icon-controller-play">&nbsp;</i>Video zu <em>Neuen Podcast anlegen</em> ansehen</a>
                    </div>
                    <!--end of col-->
                </li>
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5 order-lg-2">
                        <img alt="Image" src="{{ asset('images1/tour/einfach-bestehenden-podcast-importieren.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5 order-lg-1">
                        <h3>Bestehenden Podcast importieren</h3>
                        <h5>Einfach umziehen</h5>
                        <p>
                            Als Umzügler von einem anderen Host kannst Du <b>Deinen Podcast importieren</b>: Mit dem Import werden alle Dateien und Metadaten, die mit Deinem RSS-Feed verknüpft sind, in Dein podcaster.de Konto übernommen. Für den Import wird Dir kein Speicherplatz abgezogen!
                        </p>
                        <p>
                            Damit bleibt Dein Kanal, wie er ist. Es wird lediglich ein neuer RSS-Link erstellt und Du kannst mit dem Kennenlernen der podcaster.de Funktionen beginnen.
                        </p>
<!--                        <a href="#">More about Team Collaboration &rsaquo;</a>-->
                        <a href="{{ route('lp.video', ['video' => 8, 'slug' => 'host-wechseln-zu-podcasterde']) }}?h=1" class="btn btn-link btn-lg" target="_blank" rel="noopener noreferrer" onclick="window.open(this.href, 'video','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"><i class="icon-controller-play">&nbsp;</i>Video zum Import ansehen</a>
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    @if($reviews)
    <section class="space-sm">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h6 class="font-weight-lighter">Kundenmeinung</h6>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="media">
                        <?php
                        $review = array_shift($reviews);
                        ?>
                        <img alt="{{ $review['podcast'] }}" src="{{ asset('storage/reviews/images/' . $review['logo']) }}" class="avatar avatar-square avatar-lg" loading="lazy" />
                        <div class="media-body">
                            <p class="h4 mb-2">
                                {!! $review['cite'] !!}
                            </p>
                            <span>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</span>
                        </div>
                    </div>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    @endif

    <section class="bg-white">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Image" src="{{ asset('images1/tour/podcast-einfach-anmelden.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Podcast verbreiten</h3>
                        <h5>Einfach auf allen Diensten</h5>
                        <p>
                            Erreiche Deine Hörer dort, wo sie Podcasts hören: Mit dem Anmelde-Werkzeug kommt Dein Kanal auf die beliebtesten Podcast-Portale und Apps. Sie sind zentral aufgelistet und lassen sich von dort aus verwalten.

                            Dank unserer Partnerschaft mit Spotify ist die dortige Anmeldung problemlos und schnell erledigt. Wir wollen, dass Du gehört wirst.
                        </p>
<!--                        <a href="#">More about Phasers &rsaquo;</a>-->
                        <a href="{{ route('lp.video', ['video' => 4, 'slug' => 'anmelde-werkzeug']) }}?h=1" class="btn btn-link btn-lg" target="_blank" rel="noopener noreferrer" onclick="window.open(this.href, 'video','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"><i class="icon-controller-play">&nbsp;</i>Video zum Anmelde-Werkzeug ansehen</a>
                    </div>
                    <!--end of col-->
                </li>
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5 order-lg-2">
                        <img alt="Image" src="{{ asset('images1/tour/podcast-statistiken-einfach-verstehen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5 order-lg-1">
                        <h3>Umfangreiche Podcast-Statistiken</h3>
                        <h5>Einfach Zahlen im Blick behalten</h5>
                        <p>
                            Erfahre anhand unserer Podcast-Statistiken alles über die Anzahl, Orte und Zeiten der Zugriffe auf Deinen Kanal, die Episoden und mehr.
                        </p>
                        <p>
                            Beobachte den zeitlichen Verlauf Deiner Abonnenten- und Hörerzahlen und entwickle somit ein Gefühl für Deine Hörerschaft.
                        </p>
<!--                        <a href="#">More about Team Collaboration &rsaquo;</a>-->
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="space-sm mb-4">
        <div class="container">
            <div class="row section-intro">
                <div class="col-12 text-center">
                    <h3 class="h1">Unverbindlich testen, 30 Tage kostenlos.</h3>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <form>
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="mt-3">
                            <a id="button" href="{{ route('package.order', ['id' => 3, 'name' => 'Profi']) }}" class="btn btn-lg btn-block btn-primary">Hier registrieren</a>
                        </div>
                    </div>
                </div>
                <!--end of row-->
            </form>
        </div>
        <!--end of container-->
    </section>

    <section class="bg-white">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Podcast Episoden planen" src="{{ asset('images1/tour/einfach-podcast-episoden-planen-und-veroeffentlichen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Intuitives Podcast-Management</h3>
                        <h5>Einfach Episoden veröffentlichen und planen</h5>
                        <p>
                            Nie wieder den Wecker stellen: Richte einen Veröffentlichungszeitpunkt für Deine neuen Episoden ein. So kannst Du den üblichen Rhythmus Deiner neuen Folgen garantiert einhalten. Die neue Episode wird dann zeitgesteuert veröffentlicht. Egal, wo Du gerade bist.</p>
                        <p>
                            Außerdem kannst Du angelegte Episoden als Entwürfe speichern und später weiter bearbeiten. Eingegebene Beschreibungen und Daten gehen dann nicht verloren.
                        </p>
<!--                        <a href="#"></a>-->
                        <a href="{{ route('lp.video', ['video' => 2, 'slug' => 'episode-veroffentlichen']) }}?h=1" class="btn btn-link btn-lg" target="_blank" rel="noopener noreferrer" onclick="window.open(this.href, 'video','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"><i class="icon-controller-play">&nbsp;</i>Video zum Veröffentlichen ansehen</a>
                    </div>
                    <!--end of col-->
                </li>
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5 order-lg-2">
                        <img alt="Podcast Shownotes schreiben" src="{{ asset('images1/tour/einfach-podcast-shownotes-anlegen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5 order-lg-1">
                        <h3>Podcast-Metadaten</h3>
                        <h5>Einfach Shownotes erstellen</h5>
                        <p>
                            Erstelle erstklassige Shownotes mit unserem Textwerkzeug für Episoden-Beschreibungen, das HTML-Formatierung unterstützt.
                        </p>
                        <p>
                            Verpasse Deinen Podcast-Folgen spannende Aufreißer, informativen Mehrwert und Inhaltsangaben, auf die Deine Hörer aufmerksam werden.
                        </p>
                        <!--                        <a href="#"> &rsaquo;</a>-->
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="space-md">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h6 class="font-weight-lighter">Unterschiedlichste Organisationen nutzen podcaster</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <ul class="list-inline list-inline-large">
                        <li class="list-inline-item">
                            <img alt="Greenpeace" class="logo logo-lg" src="{{ asset('images1/referenzen/greenpeace-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Klassikradio" class="logo logo-lg" src="{{ asset('images1/referenzen/klassikradio-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Pernot Ricard" class="logo logo-lg" src="{{ asset('images1/referenzen/pernot-ricard-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Hans Seidel Stiftung" class="logo logo-lg" src="{{ asset('images1/referenzen/hans-seidel-stiftung-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="REHAU" class="logo logo-lg" src="{{ asset('images1/referenzen/rehau-logo.webp') }}" loading="lazy" style="height:45px" />
                        </li>
                    </ul>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="bg-white">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <picture>
                            <source srcset="{{ asset('images1/tour/flexibler-und-maechtiger-podcast_webplayer-zur-integration.webp') }}" type="image/webp">
                            <img
                                alt="Einfach zu gestaltender, flexibler, mächtiger Podcast Webplayer zur Integration in Webseiten"
                                src="{{ asset('images1/tour/flexibler-und-maechtiger-podcast_webplayer-zur-integration.webp') }}"
                                height="306" width="385"
                                loading="lazy"
                                class="img-fluid rounded" />
                        </picture>
                    </div>
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Anpassbarer Podcast-Player</h3>
                        <h5>Podcasts direkt in Websites abspielen</h5>
                        <p>
                            Gestalte den Player nach Deinen Bedürfnissen, um ihn in Deine eigenen Websites einzubinden.
                        </p>
                        <p>
                            Farben und Größe kannst Du so einstellen, dass sie perfekt ins Layout Deines Blogs oder Deiner Website passen.
                        </p>
                        <p>
                            Falls Du einen Blog-Post einer einzelnen Episode widmest, lassen sich auch einzelne Episoden im Player einstellen.
                        </p>
                        <p>
                            Über die Logo-Buttons können Deine Hörer den Podcast auf ihrer bevorzugten Plattform abonnieren.
                        </p>
                    </div>
                </li>
<!--                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Podcast Qualität verbessern" src="{{ asset('images1/tour/einfach-podcast-qualitaet-verbessern.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Podcast-Audio</h3>
                        <h5>Einfach Aufnahme-Qualität verbessern</h5>
                        <p>
                            Verknüpfe dein podcaster.de-Konto mit der webbasierten Post-Production-Lösung Auphonic und spare Dir damit zeitraubende Up- und Downloads zwischen verschiedenen Diensten.
                        </p>
                        <p>
                            Auphonic ermöglicht Dir das Erstellen von Presets für Deine Episoden, das Hinzufügen von In- und Outros, Metadaten und mehr.
                        </p>
                        <p>
                            So kannst Du Deine Auphonic-Vorlagen ohne Umstände für Deine Veröffentlichungen mit podcaster.de nutzen.
                        </p>
                    </div>
                </li>-->
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5 order-lg-2">
                        <img alt="Podcast Extras nutzen" src="{{ asset('images1/tour/einfach-podcast-extras-nutzen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5 order-lg-1">
                        <h3>Flexible Podcast-Pakete</h3>
                        <h5>Einfach mehr, wenn Du es brauchst</h5>
                        <p>
                            Wir kennen keine Wachstumsschmerzen. Sollten Deine Ansprüche einmal über Dein gebuchtes Paket hinauswachsen, kannst Du es einfach um gewünschte Funktionen erweitern: Mehr Speicherplatz, extra Feeds oder Mitarbeiter/innen für Dein Konto sind dank Zusatzoptionen kein Problem.
                        </p>
                        <p>
                            Übrigens kannst Du jederzeit Dein gebuchtes Paket aufstocken – ohne Mindestlaufzeiten. Sei flexibel, wir sind es auch.
                        </p>
<!--                        <a href="#">More about Team Collaboration &rsaquo;</a>-->
                        <a href="{{ route('lp.video', ['video' => 3, 'slug' => 'probephase-und-guthaben-konto']) }}?h=1" class="btn btn-link btn-lg" target="_blank" onclick="window.open(this.href, 'video','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"><i class="icon-controller-play">&nbsp;</i>Video zur Paket-Verwaltung ansehen</a>
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

@if($reviews && count($reviews) > 0)
    <section class="bg-gradient text-white space-lg">
            <div class="row">
                <div class="col-12 text-center">
                    <span class="title-decorative">Das sagen unsere Kunden über uns</span>
                </div>
            </div>
            <div class="row justify-content-center mt-4 mx-1 mx-md-5">
                @foreach($reviews as $review)
                    <div class="col-12 col-lg-4">
                        <div class="media">
                            <img alt="{{ $review['podcast'] }}" src="{{ asset('storage/reviews/images/' . $review['logo']) }}" class="avatar avatar-square avatar-lg" loading="lazy" />
                            <div class="media-body">
                                <blockquote class="h4">{!! $review['cite'] !!}</blockquote>
                                <span>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            <!--end of col-->
            </div>
            <!--end of row-->
    </section>
@endif

    <section class="bg-white">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Podcast Status prüfen" src="{{ asset('images1/tour/einfach-podcast-status-pruefen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Integrierter Podcast-Check</h3>
                        <h5>Einfach Podcast-Status prüfen</h5>
                        <p>
                            Anfangs kann es überfordernd sein, an alles zu denken.
                        </p>
                        <p>
                            Ob Dein Kanal auf technischer Seite grünes Licht hat, siehst Du auf einem Blick im Status-Check.
                        </p>
                        <p>
                            Damit Du Dich auf Deine Inhalte konzentrieren kannst, und nicht auf die Technik.
                        </p>
<!--                        <a href="#">More about Phasers &rsaquo;</a>-->
                    </div>
                    <!--end of col-->
                </li>
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5 order-lg-2">
                        <img alt="Image" src="{{ asset('images1/tour/podcast-gemeinsam-machen.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5 order-lg-1">
                        <h3>Gemeinsame Podcast-Sache</h3>
                        <h5>Einfach gemeinsam Podcasts machen</h5>
                        <p>
                            Gemeinsame Sache machen ist bei uns ganz einfach. Arbeitet im Team ganz einfach gemeinsam an einem oder mehreren Podcast-Projekten.
                        </p>
                        <!--                        <a href="#"></a>-->
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->


    <section class="bg-white flush-with-above">
        <div class="container">
            <ul class="feature-list feature-list-lg">
                <li class="row justify-content-around align-items-center">
                    <div class="col-9 col-md-6 col-lg-5">
                        <img alt="Einfach DSGVO konform" src="{{ asset('images1/tour/podcast-einfach-dsgvo-konform.webp') }}" class="img-fluid rounded" loading="lazy" />
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-6 col-lg-5">
                        <h3>Podcast legal</h3>
                        <h5>Einfach DSGVO-konform podcasten</h5>
                        <p>
                            Nach dem EU-Gesetz der Datenschutz-Grundverordnung sehen wir uns verantwortlich für Transparenz und schützen Deine personenbezogenen Daten. Ausserdem erfolgen alle Datenerhebungen, Speicherungen und Verarbeitungen in Deutschland.
                        </p>
<!--                        <a href="#">More about Phasers &rsaquo;</a>-->
                    </div>
                    <!--end of col-->
                </li>
            </ul>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="space-md">
        <div class="container">
            <div class="row section-intro">
                <div class="col-12 text-center">
                    <h3 class="h1">Einfach testen! Melde Dich jetzt an für Deine kostenlose 30-tägige Probephase.</h3>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <form>
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="mt-3">
                            <a id="button" href="{{ route('packages') }}" class="btn btn-lg btn-block btn-primary">Unsere Pakete ansehen</a>
                        </div>
                    </div>
                </div>
                <!--end of row-->
            </form>
        </div>
        <!--end of container-->
    </section>
@endsection('content')

@push('scripts')
    <script src="{{ asset(mix('js1/jquery-3.6.0.min.js')) }}"></script>
    <script src="https://support.podcaster.de/assets/chat/chat.min.js"></script>
    <script>
        $(function() {
            const chat = new ZammadChat({
                background: '#212529',
                fontSize: '12px',
                chatId: 1,
                onConnectionEstablished(data) {
                    chat.send('chat_session_notice', {
                        session_id: chat.sessionId,
                        message: '{{auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name . ' [' . auth()->user()->email . '] (' . auth()->id() . ')' : 'Gast' }}',
                    });
                }
            });
        });
    </script>
@endpush
