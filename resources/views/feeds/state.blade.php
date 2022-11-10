@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('feeds.state', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h1 class="h2 mb-2">@lang('feeds.header_state', ['feed' => $feed->rss['title']])</h1>
                    <span>@lang('feeds.subheader_state')</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="height-70">
        <div class="container" id="app" data-type="state">

            <div class="text-center">
                <div class="spinner-grow m-5" role="status" v-if="false">
                    <span class="sr-only">@lang('feeds.text_loading_data')</span>
                </div>
            </div>

            <websocket-connection-check></websocket-connection-check>

            <state-check feed="{{ $feed->feed_id }}" uuid="{{ $uuid }}"></state-check>

            <section>
                <h6>Legende</h6>
                <p>Die Tests sind teilweise voneinander abhängig. Fehler von oben nach unten beheben.</p>
                <ul class="list-group list-group-horizontal">
                    <li class="list-group-item">
                        <b-spinner class="ml-auto" label="Daten werden geladen" v-b-popover.click.hover="'Wenn ich nach 10 Sekunden neben den Tests noch sichtbar bin, lade die Seite erneut.'" /> Ich lade!
                    </li>
                    <li class="list-group-item">
                        <b-badge class="p-1" pill variant="success" v-b-popover.click.hover="'Ich bin die Gute!'"><i class="icon-check"></i></b-badge>  Alles ist gut!
                    </li>
                    <li class="list-group-item">
                        <b-badge class="p-1" pill variant="warning" v-b-popover.click.hover="'Ich bin neutral.'"><i class="icon-bell"></i></b-badge>  Wirf besser einen Blick drauf!
                    </li>
                    <li class="list-group-item">
                        <b-badge class="p-1" pill variant="danger" v-b-popover.click.hover="'Ich bin der Böse!'"><i class="icon-flag"></i></b-badge>  Oh, oh. Da musst du ran!
                    </li>
                </ul>
                <p class="pt-2 text-muted font-weight-lighter">Wenn du über ein Icon fährst, erhältst du einen Hinweis. Mit Klick auf das Icon bleibt der Hinweis angezeigt. Probier es aus!</p>
            </section>
        </div>
    </section>

{{--    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <h5>Feedback - Deine Meinung zählt!</h5>
                    <a href="{{ route('feedback.create') }}?type=4" class="btn btn-secondary"
                       onclick="window.open(this.href,'feature_statecheck','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0'); return false;">Rückmeldung
                        zum Status-Check geben</a>
                </div>
            </div>
        </div>
    </section>--}}
@endsection('content')
