@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('show_add', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-11">
                    <h2 class="h5 mb-2">{{$feed->rss['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('shows.header_add_show')</h1>
                    <span>{{ trans('shows.subheader_add_show', ['title' => $feed->rss['title']]) }}</span>
                </div>
                <div class="col-1">
                    <a href="{{ route('lp.video', ['video' => 2, 'slug' => 'episode-veroffentlichen']) }}">
                        <img src="{{ asset('images1/help/hilfe-video.png') }}" alt="{{ trans('help.alt_help_video') }}" class="m-1">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="p-md-5 p-lg-3 p-sm-1 p-0">
        <div class="container-fluid card p-lg-3 p-xl-2 p-md-3 p-sm-2 p-1" id="app" data-type="addshow">
            <div class="text-center" v-if="false">
                <div class="spinner-grow m-3" role="status">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>

            <add-show
                :feed="{{json_encode($feed)}}" {{ $canSchedulePosts ? ' can-schedule-posts ' : '' }}
                guid="{{$guid}}"
                :count-shows="{{ $countShows }}"></add-show>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <h5>Feedback - Deine Meinung zählt!</h5>
                    <a href="{{ route('feedback.create') }}?type=9" class="btn btn-secondary"
                       onclick="window.open(this.href,'feature_submit','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0'); return false;">Rückmeldung
                        zur "Episode anlegen"-Seite geben</a>
                </div>
{{--                <div class="col-lg-4 col-12">
                    <h6>Offene Punkte</h6>
                    <ul>
                        <li>Formulare vorausfüllen</li>
                        <li>Weitere Dienste finden und einbinden</li>
                    </ul>
                </div>--}}
            </div>
        </div>
    </section>
@endsection('content')
