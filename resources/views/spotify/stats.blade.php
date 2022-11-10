@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('spotifystats') }}
@endsection

@section('content')
    <div class="container">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">@lang('spotify.header_stats')</h1>
                        <span>@lang('spotify.subheader_stats')</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section>
            <div class="row">
                <div class="col col-lg-12" id="app" data-type="spotifystats">
                    <spotify-stats></spotify-stats>
                </div>
            </div>
        </section>

    </div>
@endsection('content')

{{--
@push('scripts')
    <script src="{{ mix('js1/spotifystatistics.js') }}"></script>
@endpush--}}
