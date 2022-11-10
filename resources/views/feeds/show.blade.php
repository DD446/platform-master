@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('feeds_channel', $feed) }}
@endsection

@section('content')
    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h2 class="h5 mb-2">{{$feed->rss['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('feeds.header_channel')</h1>
                    <span>@lang('feeds.subheader_channel', ['title' => $feed->rss['title']])</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section class="flush-with-above space-0">
        <div class="bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-12" id="app" data-type="feeddetails">
                        <div class="text-center" v-if="false">
                            <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">...</span>
                            </div>
                        </div>
                        <feed-details :feed="{{$feed}}"></feed-details>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection('content')
