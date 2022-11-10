@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('feedwizard') }}
@endsection

@section('content')

    <section class="space-sm">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <a href="{{ route('home') }}">
                        <img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo.svg') }}" class="img img-fluid" style="min-height: 80px"  />
                    </a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <div class="container" id="app" data-type="feedwizard">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    <div class="text-center" id="feedFormLoader" v-if="false">
                        <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">...</span>
                        </div>
                    </div>
                    @if($cFeed['available'] > 0)
                        <feed-form-wizard
                            :author="'{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}'"
                            :email="'{{ auth()->user()->email }}'"
                            :username="'{{ auth()->user()->username }}'"
                            :local-domains="{{json_encode(array_combine(config('app.local_domains'),config('app.local_domains')))}}"></feed-form-wizard>
                    @else
                        <div class="container card">
                            <div class="text-center alert-warning m-4 p-4">
                                {!! trans('feeds.text_hint_no_feeds_available', ['route' => route('extras.index')]) !!}
                            </div>
                        </div>
                    @endif

                    @if($hideNav)
                    <div class="mt-5 text-lg-right">
                        <span class="text-small"><a href="{{ route('feeds') }}">@lang('feeds.link_skip_wizard')</a></span>
                    </div>
                    @endif
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection('content')
