@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('press') }}
@endsection

@section('content')

    <section class="space-md bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_press')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_press')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="col-12">
                    <h5>{{trans('pages.header_press_about', ['name' => config('app.name')])}}</h5>
                    <p class="mt-3">
                        Einfach podcasten. Seit 2007 ist podcaster als Podcast-Host aktiv.
                        Gegründet von Fabio Bacigalupo und jahrelang in Eigenregie
                        betrieben, wächst podcaster konstant und nachhaltig.
                    </p>
                    <p class="mt-3">
                        Kunden laden hier ihre Podcasts hoch und podcaster verbreitet sie im Internet. So
                        gelangen die Podcasts an alle gängigen Plattformen oder Streamingdienste und damit zu den Hörern.
                    </p>
                    <p class="mt-3">
                        Dank einfacher Bedienung, persönlichem Support und jahrelangem Know-How konnte
                        podcaster sich seinen festen Platz als Podcast-Hosting-Spezialist sichern.
                        Flexible Angebote und individuelle Lösungen machen uns zu einem zuverlässigen Partner.
                        Unsere 5.000+ Kunden reichen von Hobby-Podcastern bis zu Dax-Unternehmen.
                    </p>
                    <p class="mt-3">
                        Wir bei podcaster wissen, dass die ersten Schritte manchmal etwas schwierig
                        sein können. Deswegen halten wir auch gerade für Einsteiger das
                        Podcasting absolut einfach. Als langfristiger Partner machen wir in
                        nur wenigen Schritten Podcast-Fans zu waschechten Podcastern.
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 mt-4 mt-sm-0">
                <h5>{{trans('pages.header_press_contact')}}</h5>

                <figure>
                    <img src="{{ asset('images1/fabio_bacigalupo_presse_klein.jpg') }}" alt="Fabio Bacigalupo" class="img img-thumbnail rounded-circle rounded-sm" style="max-width: 150px">
                    <figcaption>Ihr Ansprechpartner: <br>Fabio Bacigalupo<br>(Gründer und Inhaber)</figcaption>
                </figure>

                <p class="mt-3">
                    Kontaktieren Sie mich gerne für Ihre Presseanfragen.
                </p>
                <dl class="mt-3">
                    <dt>E-Mail:</dt> <dd><a href="mailto:presse+website@podcaster.de">presse[at]podcaster[punkt]de</a></dd>
                </dl>
                <dl class="mt-3">
                    <dt>Telefon:</dt> <dd><a href="tel:004930549072654">+49(0)30-549072654</a></dd>
                </dl>
                <dl class="mt-3">
                    <dt>Web:</dt> <dd><a href="https://www.podcaster.de">www.podcaster.de</a></dd>
                </dl>
                <dl class="mt-3">
                    <dt>Impressum:</dt> <dd><a href="{{ route('page.imprint') }}">www.podcaster.de</a></dd>
                </dl>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-4">
                <h4>{{trans('pages.header_press_numbers')}}</h4>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5>{{trans('pages.header_press_map')}}</h5>
                        <a href="{{ asset('pdf1/pressemappe_podcasterDe_20220217.pdf') }}" download="pressemappe_podcasterDe_20220217.pdf">
                            <img src="{{ asset('images1/press/pressemappe_cover.webp') }}" alt="podcaster.de Pressemappe">
                        </a>
                    </div>
                    <div class="col-12 col-md-6 mt-3 mt-sm-1 mt-md-0">
                        <h5>{{trans('pages.header_press_cornerstones')}}</h5>
                        <ul class="mt-3">
                            <li>Seit 2007 am Markt</li>
                            <li>Über 5.000 Kunden</li>
                            <li>Kunden aus über 80 Ländern</li>
                            <li>Über 10.000 Podcasts</li>
                            <li>Über 150 TB Datentraffic</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-4">
                <h4>{{trans('pages.header_press_pictures')}}</h4>
                <p class="mt-3">
                    Die Logos können Sie frei herunterladen und verwenden. Alle Logos gesammelt können Sie <a href="{{ asset('images1/press/podcaster-logos.zip') }}" download="podcaster-logos.zip">hier als
                        .zip-Datei</a> herunterladen.
                </p>

                <div class="row">
                    <div class="col-12 col-md-4 mt-3 mt-sm-1 mt-md-0">
                        <h6>Farbig</h6>
                        <img src="{{ asset('images1/press/logo/color/podcaster.de-250px.png') }}" alt="podcaster.de farbiges Logo" class="img m-4">
                        <ul>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de-250px.png') }}" download="podcaster.de-250px.png">PNG (250px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de-1000px.png') }}" download="podcaster.de-1000px.png">PNG (1000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de-3000px.png') }}" download="podcaster.de-3000px.png">PNG (3000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de.svg') }}" download="podcaster.de.svg">SVG</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de.pdf') }}" download="podcaster.de.pdf">PDF</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/color/podcaster.de.eps') }}" download="podcaster.de.eps">EPS</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-sm-1 mt-md-0">
                        <h6>Schwarz</h6>
                        <img src="{{ asset('images1/press/logo/black/podcaster.de-250px.png') }}" alt="podcaster.de farbiges Logo" class="img m-4">
                        <ul>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de-250px.png') }}" download="podcaster.de-250px.png">PNG (250px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de-1000px.png') }}" download="podcaster.de-1000px.png">PNG (1000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de-3000px.png') }}" download="podcaster.de-3000px.png">PNG (3000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de.svg') }}" download="podcaster.de.svg">SVG</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de.pdf') }}" download="podcaster.de.pdf">PDF</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/black/podcaster.de.eps') }}" download="podcaster.de.eps">EPS</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-sm-1 mt-md-0">
                        <h6>Weiß</h6>
                        <img src="{{ asset('images1/press/logo/white/podcaster.de-250px.png') }}" alt="podcaster.de farbiges Logo" class="img m-4">
                        <ul>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de-250px.png') }}" download="podcaster.de-250px.png">PNG (250px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de-1000px.png') }}" download="podcaster.de-1000px.png">PNG (1000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de-3000px.png') }}" download="podcaster.de-3000px.png">PNG (3000px)</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de.svg') }}" download="podcaster.de.svg">SVG</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de.pdf') }}" download="podcaster.de.pdf">PDF</a>
                            </li>
                            <li>
                                <a href="{{ asset('images1/press/logo/white/podcaster.de.eps') }}" download="podcaster.de.eps">EPS</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
