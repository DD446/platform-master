@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('audiotakes') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('audiotakes.header_contracts')}}</h1>
                    <span class="title-decorative">{{trans('audiotakes.subheader_contracts')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container" id="app" data-type="audiotakes">

        <div class="row mb-5">
            <div class="col-5">
                <a href="https://www.podcastpioniere.de/?mtm_campaign=VermarktungsseitePodcaster" target="_blank">
                    <img src="{{asset('/images1/podcast-pioniere-250px.png')}}" alt="Podcast Pioniere Logo" class="img img-fluid">
                </a>
            </div>
            <div class="col-7 align-self-center">
                <h4>
                    Fragen? Ruf an: <a href="tel:004930549072656">+49(0)30-549072656</a> oder <a href="mailto:info@podcastpioniere.de?subject=Fragen">maile uns</a>
                </h4>
            </div>
        </div>

        @if($contracts->count() > 0)
        <div class="row">
            <div class="col-12">
                <h3>Mein Vermarktungsguthaben <audiotakes-funds :funds="{{$payoutFundsSum}}"></audiotakes-funds></h3>
            </div>
            <div class="col-12 mt-3">
                <div class="p-3 p-md-5 bg-white">
                    <div class="text-center" v-if="false">
                        <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">...</span>
                        </div>
                    </div>
                    <audiotakes-transfer-funds funds="{{$payoutFundsSum}}" :contracts="{{$contractPartners}}" :country-list="{{ json_encode($countryList) }}"></audiotakes-transfer-funds>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h3>Meine Ausschüttungshistorie</h3>
            </div>
            <div class="col-12 mt-3">
                <div class="p-5 bg-white">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h4>Verrechnung</h4>
                            <ul class="list-group">
                            @forelse(\App\Models\AudiotakesPodcasterTransfer::owner()->paginate() as $transfer)
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $transfer->funds }} {{ \App\Models\AudiotakesContract::DEFAULT_CURRENCY }}</h5>
                                        <small>{{ $transfer->created_at->isoFormat('DD.MM.YYYY') }}</small>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    {{ trans('audiotakes.message_empty_no_podcaster_transfers') }}
                                </li>
                            @endforelse
                            </ul>
                        </div>
                        <div class="col-12 col-md-6">
                            <h4>Auszahlung</h4>
                            <ul class="list-group">
                                @forelse(\App\Models\AudiotakesBankTransfer::owner()->paginate() as $transfer)
                                    <li class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ $transfer->funds }} {{ \App\Models\AudiotakesContract::DEFAULT_CURRENCY }}</h5>
                                            @if($transfer->is_paid)
                                            <span class="alert alert-success mb-0 py-1">Überwiesen</span>
                                            @else
                                                <span class="alert alert-info mb-0 py-1">In Bearbeitung</span>
                                            @endif
                                            <small>{{ $transfer->created_at->isoFormat('DD.MM.YYYY') }}</small>
                                            <div>
                                                <a href="{{ route('audiotakes.creditvoucher', ['id' => $transfer->id]) }}" class="btn btn-primary btn-sm" download="{{ $transfer->billing_number }}{{ \App\Models\AudiotakesBankTransfer::CN_EXTENSION }}">PDF</a>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        {{ trans('audiotakes.message_empty_no_podcaster_transfers') }}
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h3>Meine Verträge</h3>
            </div>
            <div class="col-12 mt-3">
                <div class="p-3 p-md-5 bg-white">
                    <table class="table">
                        <tr>
                            <th>{{ trans('audiotakes.label_identifier') }}</th>
                            <th>{{ trans('audiotakes.label_assigned_feed') }}</th>
                            <th>{{ trans('audiotakes.label_contract_partner') }}</th>
                            <th>{{ trans('audiotakes.label_date_created') }}</th>
                            <th>{{ trans('audiotakes.label_state') }}</th>
                            <th>{{ trans('audiotakes.label_stats') }}</th>
                        </tr>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>
                                {{ $contract->identifier }}
                            </td>
                            <td>
                                <a href="{{ route('feeds') }}#/podcast/{{ $contract->feed_id }}">
                                    {{ $contract->feed_id }}
                                </a>
                            </td>
                            <td>
                                {{ $contract->first_name }} {{ $contract->last_name }}@if($contract->organisation), {{ $contract->organisation }}@endif
                            </td>
                            <td>
                                {{ $contract->audiotakes_date_accepted->formatLocalized('%d.%m.%Y') }}
                            </td>
                            <td>
                                @php
                                    $feed = null;
                                    $feed = \Illuminate\Support\Arr::first($feeds, function ($value) use ($contract) {
                                        return $value->feed_id == $contract->feed_id;
                                    });
                                @endphp
                                @if($contract->trashed() || !$feed)
                                    <span class="alert alert-danger mb-0 py-1">
                                        @lang('audiotakes.state_canceled')
                                    </span>
                                @else
                                    @isset($feed->settings['audiotakes'])
                                        <span class="alert alert-success mb-0 py-1">
                                            @lang('audiotakes.state_active')
                                        </span>
                                    @else
                                        <span class="alert alert-warning mb-0 py-1">
                                            @lang('audiotakes.state_inactive')
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <a href="/audiotakes/statistiken/{{$contract->identifier}}">Statistiken</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <span class="font-weight-bold">
                                    @lang('audiotakes.no_contracts_signed')
                                </span>
                            </td>
                        </tr>
                    @endforelse
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($contracts->count() == 0)
        <section class="bg-white p-3 p-md-5">
            <div class="row">
                <div class="col-12">
                    <h4 class="display-3">Sei ein Pionier!</h4>
                    <h5 class="display-5">Schließ Dich über 350 Pionieren an.</h5>
                    <p class="font-weight-bolder">
                        Über den Dienst Podcast Pioniere kannst Du durch Werbe-Einspielungen Geld mit Deinen Podcasts verdienen. Es fallen keine extra Kosten für Dich an. Podcast Pioniere unterstützt auch kleine Podcasts direkt ab Start. Wir bringen die Werbung, Du die Hörer. Du kannst über unseren Adserver dazu eigene Kampagnen schalten. Die Monetarisierung startet automatisch direkt nach Vertragsabschluss und kann jederzeit pausiert werden.
                    </p>
                    <p>Viele Fragen rund um den Dienst, bekommst Du <a href="/faq/monetarisierung-10">auf unseren FAQ-Seiten zum Thema Monetarisierung</a> erklärt.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12">
                    <p class="">
                        Um Deinen Podcast von den <a href="https://www.podcastpioniere.de" target="_blank">Podcast Pionieren</a> vermarkten zu lassen, musst Du zuerst einen Vermarktungsvertrag mit der audiotakes GmbH - der Gesellschaft hinter den Podcast Pionieren - abschließen.
                    </p>
                </div>
            </div>
        </section>
        @endif

        <form method="get" action="{{ url('/audiotakes/vertrag/anlegen') }}">
            <div class="row mt-5">
                <div class="col-12">
                    <h3>Neuen Vertrag abschließen</h3>
                </div>
                    @if($feeds->count() > 0)
                    <div class="col-12 mt-3">
                        <div class="p-3 p-md-5 bg-white">
                            <div class="form-group">
                                <select class="custom-select custom-select-lg mb-3" required name="feed_id">
                                    <option selected value="" disabled>{{ trans('audiotakes.option_choose_feed') }}</option>
                                    @foreach($feeds as $feed)
                                    @php
                                        $isDisabled = null;
                                        $contains = $contracts->contains(function ($value) use ($feed) {
                                            return  $value->feed_id == $feed->feed_id;
                                        });
                                        if ($contains) {
                                            $isDisabled = ' disabled ';
                                        }
                                    @endphp
                                    <option value="{{$feed->feed_id}}" {{ $isDisabled }}>{{$feed->rss['title']}} @if($isDisabled) (@lang('feeds.hint_contract_signed'))@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg float-right">{{ trans('audiotakes.button_create_contract') }}</button>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="col-12">
                            Du musst zuerst <a href="{{ route('podcast.wizard') }}">einen Podcast anlegen</a>.
                        </div>
                    @endif
            </div>
        </form>

        <p class="pt-4 pb-2">
            <span class="text-muted text-small">
                <a href="https://www.podcastpioniere.de" class="text-reset" target="_blank">Podcast Pioniere</a> ist ein Service der <a href="https://audiotakes.net" class="text-reset" target="_blank">audiotakes GmbH</a>.
            </span>
        </p>
    </section>
@endsection('content')
