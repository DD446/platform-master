@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('show_share', $feed, $show) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h2 class="h5 mb-2">{{$show['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('shows.header_share_show')</h1>
                    <span>{{ @trans('shows.subheader_share_show', ['title' => $feed->rss['title']]) }}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section class="p-md-5 p-lg-3 p-sm-1 p-0">
        <div class="container card p-lg-3 p-xl-2 p-md-3 p-sm-2 p-1" id="app" data-type="shareshow">
            <div class="text-center" v-if="false">
                <div class="spinner-grow m-3" role="status">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>

            <share-show feed-id="{{$feed->feed_id}}" :show="{{json_encode($show)}}" :sharing-opts="{{json_encode($sharingOpts)}}"></share-show>
        </div>
    </section>

<!--    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <h5>Feedback - Deine Meinung zählt!</h5>
                    <a href="{{ route('feedback.create') }}?type=12" class="btn btn-secondary"
                       onclick="window.open(this.href,'feature_teams','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0'); return false;">Rückmeldung
                        zum Teilen-Feature geben</a>
                </div>
            </div>
        </div>
    </section>-->
@endsection('content')
