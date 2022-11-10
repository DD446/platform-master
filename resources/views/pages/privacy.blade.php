@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('privacy') }}
@endsection

@section('content')

    <section class="space-md bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_privacy')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_privacy')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12 col-md-12">
                    @include('parts.privacy_details')
                </div>
            </div>
            <div class="row pt-5">
                <div class="col-12 col-lg-12 col-md-12">
                    <p>
                        Sie können unsere <a href="{{ asset('podcaster-datenschutzerklaerung.pdf') }}">Datenschutzerklärung hier herunterladen</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
