@extends('main')

@section('content')
    <section class="space-sm">
        <div class="container d-none d-lg-block d-xl-block">
            <div class="row">
                <div class="col text-center">
                    <a href="{{ route('home') }}">
                        <img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}" />
                    </a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <section class="height-80 flush-with-above">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-6 mb-4">
                    <img alt="Image" src="{{ asset('images1/bestellung_hinweis_1.jpg') }}" class="w-100 rounded" />
                </div>
                <!--end of col-->
                <div class="col-12 col-md-7 col-lg-5 mb-4 text-center text-md-left">
                    <h1 class="display-3">@lang('login.header_preregistration_hint')</h1>
                    <h2 class="lead">@lang('login.subheader_preregistration_hint')</h2>

                    <ul class="row feature-list feature-list-sm">

                        <li class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ trans_choice('login.preregistration_steps_header', 1) }}</h4>
                                    <p class="card-text text-body">{{ trans_choice('login.preregistration_steps_body', 1) }}</p>
                                </div>

                                <div class="card-footer card-footer-borderless d-flex justify-content-between">
                                    <div class="text-info">
                                        {{ session('email') }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!--end of col-->

                        <li class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ trans_choice('login.preregistration_steps_header', 2) }}</h4>
                                    <p class="card-text text-body">{{ trans_choice('login.preregistration_steps_body', 2) }}</p>
                                </div>

                                <div class="card-footer card-footer-borderless d-flex justify-content-between">
                                    <div class="text-info">
                                        @lang('login.mail_preregistration_subject')
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!--end of col-->

                        <li class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ trans_choice('login.preregistration_steps_header', 3) }}</h4>
                                    <p class="card-text text-body">{{ trans_choice('login.preregistration_steps_body', 3) }}</p>
                                </div>
                            </div>
                        </li>
                        <!--end of col-->

                    </ul>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section class="space-sm flush-with-above">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <span class="text-muted text-small">@lang('login.made_with_love')</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
@endsection

@push('scripts')
    <script>
        var _mtm = _mtm || [];
        _mtm.push({
            'contentCategory': '{{ $package->package_name }}',
            'packageCost': '{{ $package->monthly_cost }}',
            'currency': '{{ $package->currency }}',
        });
    </script>
@endpush
