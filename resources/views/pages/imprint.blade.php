@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('imprint') }}
@endsection

@section('content')

    <section class="space-md bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_imprint')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_imprint')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container">

        <div class="row">
            <div class="col-12 col-lg-3 col-sm-12">
                <h6>{{trans('contact_us.side_header_address')}}:</h6>
            </div>

            <div class="col-12 col-lg-9 col-sm-12">
                Fabio Bacigalupo<br>
                podcaster.de<br>
                Brunnenstraße 147<br>
                10115 Berlin<br>
                Deutschland
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-lg-3 col-sm-12">
                <h6>{{trans('contact_us.side_header_phone')}}:</h6>
            </div>

            <div class="col-12 col-lg-9 col-sm-12">
                <p>+49 (0)30 549072654</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-lg-3 col-sm-12">
                <h6>{{trans('contact_us.side_header_email')}}:</h6>
            </div>

            <div class="col-12 col-lg-9 col-sm-12">
                <p>
                    <a href="{{route('contactus.create')}}">kontakt@podcaster.de</a>
                </p>
            </div>
        </div>

    </section>
@endsection
