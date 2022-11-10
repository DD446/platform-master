@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('audiotakes_add_contract') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('audiotakes.header_signup')}}</h1>
                    <span class="title-decorative">{{trans('audiotakes.subheader_signup', ['title' => $feed->rss['title']])}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container-fluid" id="app" data-type="audiotakes">

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

        <div>
            <div class="text-center mt-4">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <audiotakes-sign-up
                :countries="{{ json_encode($countries) }}"
                :userdata="{{$userdata}}"
                :feed-id="'{{ $feed->feed_id }}'"
                :feed-title="'{{ json_encode($feed->rss['title']) }}'"></audiotakes-sign-up>
        </div>
    </section>

@endsection('content')
