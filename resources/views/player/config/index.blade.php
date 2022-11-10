@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('playerconfig') }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('player.header_configurator')}}</h1>
                    <span class="title-decorative">{{trans('player.subheader_configurator')}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="p-1 p-md-2 p-lg-4">
        <div class="container-fluid card p-1 p-md-1 p-lg-4" id="app" data-type="playerconfigurator">
            <div class="text-center">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <player-configurator
                username="{{ auth()->user()->username }}"
                url="{{ config('app.url') }}"
                {{ $canEmbed ? ' can-embed ' : '' }}
                {{ $canCreatePlayerConfigurations ? ' can-create-player-configurations ' : '' }}
                {{ $canUseCustomPlayerStyles ? ' can-use-custom-player-styles ' : '' }}
                amount-player-configurations="{{ $amountPlayerConfigurations }}"></player-configurator>
        </div>
    </section>

{{--    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <h5>Feedback - Deine Meinung zählt!</h5>
                    <a href="{{ route('feedback.create') }}?type=6" class="btn btn-secondary"
                       onclick="window.open(this.href,'feature_player','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0'); return false;">Rückmeldung zum Player-/Konfigurator geben</a>
                </div>
                <div class="col-lg-4 col-12">
                    <h6>Offene Punkte</h6>
                    <ul>
                        <li>"Einfacher Player" freischalten</li>
                        <li>"Podcaster Player" freischalten</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-12">
                    <h6>Erledigte Punkte</h6>
                    <ul>
                        <li><s>"Web-Player" freischalten</s></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>--}}

@endsection('content')
