@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('feeds.submit', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h1 class="h2 mb-2">@lang('feeds.header_submit')</h1>
                    <span>@lang('feeds.subheader_submit', ['feed' => $feed->rss['title']])</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section>
        <div class="container card p-4" id="app" data-type="submit">
            <div class="text-center">
                <div class="spinner-grow m-5" role="status" v-if="false">
                    <span class="sr-only">@lang('feeds.text_loading_data')</span>
                </div>
            </div>
            <alert-container></alert-container>
            <submit feed="{{ $feed->feed_id }}" uuid="{{ $uuid }}"></submit>
        </div>
    </section>

@endsection('content')
