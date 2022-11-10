@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('spotify') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/spotify_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
        {{--{{ $page->getFirstMedia('bg') }}--}}

        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <span class="title-decorative">{{trans('spotify.subheader_submission')}}</span>
                    <h1 class="display-4">{{trans('spotify.header_submission')}}</h1>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container" id="app" data-type="spotify">
        <h3 class="display-3">@lang('spotify.label_podcast_list')</h3>

        <p>
            Mit der Anmeldung ermächtige ich podcaster.de, meine(n) Podcast(s) kostenlos und unentgeltlich in meinem Namen bei Spotify zu veröffentlichen.
        </p>

        <ul>
            <li>
                Ich verpflichte mich, keine vorproduzierte Werbung in meinem Podcast zu veröffentlichen (vom Moderator eingesprochene Sponsoring-Beiträge sind in Ordnung).
            </li>
            <li>
                Ich verpflichte mich, keine Werbung von Spotify-Konkurrenzprodukten (z.B. anderen Streamingdiensten oder Podcast-Plattformen) in meinem Podcast zu machen.
            </li>
            <li>
                Ich akzeptiere, dass bei Verstoß gegen diese Vereinbarung, mein Podcast aus dem Spotify-Verzeichnis fristlos und ohne Anspruch auf Entschädigung entfernt wird.
            </li>
        </ul>

        <div>
            <div class="text-center mt-4">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <spotify></spotify>
        </div>
    </section>

@endsection('content')
