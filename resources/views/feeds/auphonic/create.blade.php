@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('show_add', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h2 class="h5 mb-2">{{$feed->rss['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('shows.header_add_show')</h1>
                    <span>{{ @trans('shows.subheader_add_show', ['title' => $feed->rss['title']]) }}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
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
@endsection('content')
