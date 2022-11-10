@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('deezer') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <span class="title-decorative">{{trans('deezer.subheader_submission')}}</span>
                    <h1 class="display-4">{{trans('deezer.header_submission')}}</h1>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container" id="app" data-type="deezer">
        <h3 class="display-3">@lang('deezer.label_podcast_list')</h3>

        <p>
            @lang('deezer.hint_legal_submission')
        </p>

        <div>
            <div class="text-center mt-4">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <deezer></deezer>
        </div>
    </section>

@endsection('content')
