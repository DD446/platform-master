@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('shows', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h2 class="h5 mb-2">{{$feed->rss['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('shows.header')</h1>
                    <span>{{ @trans('shows.subheader', ['title' => $feed->rss['title']]) }}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section>
        <div class="container p-4 card" id="app" data-type="shows">
            <div class="text-center" v-if="false">
                <div class="spinner-grow m-3" role="status">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <alert-container></alert-container>

            <shows feed-id="{{$feed->feed_id}}"></shows>
        </div>
    </section>
@endsection('content')
