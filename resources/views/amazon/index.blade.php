@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('amazon') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/spotify_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
        {{--{{ $page->getFirstMedia('bg') }}--}}

        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <span class="title-decorative">{{trans('amazon.subheader_submission')}}</span>
                    <h1 class="display-4">{{trans('amazon.header_submission')}}</h1>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container" id="app" data-type="amazon">
        <h3 class="display-3">@lang('amazon.label_podcast_list')</h3>

        <p>
            @lang('amazon.hint_legal_submission')
        </p>

        <div>
            <div class="text-center mt-4">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <amazon></amazon>
        </div>
    </section>

@endsection('content')
