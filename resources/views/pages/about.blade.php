@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('aboutus') }}
@endsection

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_team')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_team', ['service' => config('app.name')])}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{--<img src="{{ asset('images1/podcaster_logo.svg') }}" alt="podcaster Logo" class="card-img img-fluid m-5">--}}

                    <div class="card-body">
                        <div class="card-header-borderless">
                        @lang('pages.text_about_us')
                    </div>
                    </div>

                    <div class="card-body">
                        <div class="card-header-borderless mb-3">
                            <h5>@lang('pages.header_team_meet')</h5>
                        </div>

                        <div class="card-text mb-3">
                            @lang('pages.text_team')
                        </div>

                        <div class="card-text">

                            <ul class="feature-list row justify-content-center">

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Fabio Bacigalupo" src="{{ asset('images1/team/fabio-bacigalupo-gruender.jpg') }}" class="img-fluid rounded mb-1" style="max-width: 150px" />
                                    <h5 class="mb-0">Fabio Bacigalupo</h5>
                                    <span>@lang('pages.subtitle_ceo')</span>
<!--                                    <ul class="list-inline list-links mt-2">
                                        <li class="list-inline-item">
                                            <a href="https://twitter.com/fabbaci" target="_blank" rel="nofollow noopener"><i class="socicon-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="https://linkedin.com/in/berlinwithfabio" target="_blank" rel="nofollow noopener"><i class="socicon-linkedin"></i></a>
                                        </li>
                                    </ul>-->
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Bastian Albert" src="{{ asset('images1/team/bastian-albert-business-development.jpg') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Bastian Albert</h5>
                                    <span>Business Development</span>
                                    <!--                                    <ul class="list-inline list-links mt-2">
                                                                        </ul>-->
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Steffen Wrede" src="{{ asset('images1/team/steffen-wrede-chefredakteur.jpg') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Steffen Wrede</h5>
                                    <span>Chefredakteur</span>
                                    <ul class="list-inline list-links mt-2">
{{--                                        <li class="list-inline-item"><a href="#"><i class="socicon-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-linkedin"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-dribbble"></i></a>
                                        </li>--}}
                                    </ul>
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Maximilian Hurlebaus" src="{{ asset('images1/team/max-hurlebaus-volontaer.jpg') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Maximilian Hurlebaus</h5>
                                    <span>Online-Redakteur</span>
                                    <ul class="list-inline list-links mt-2">
{{--                                        <li class="list-inline-item"><a href="#"><i class="socicon-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-linkedin"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-dribbble"></i></a>
                                        </li>--}}
                                    </ul>
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Niklas Bielesch" src="{{ asset('images1/team/niklas-bielesch-support.jpg') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Niklas Bielesch</h5>
                                    <span>Support</span>
<!--                                    <ul class="list-inline list-links mt-2">
                                    </ul>-->
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Naima B." src="{{ asset('images1/no-picture.png') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Naima B.</h5>
                                    <span>IT</span>
                                    <ul class="list-inline list-links mt-2">
{{--                                        <li class="list-inline-item"><a href="#"><i class="socicon-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-linkedin"></i></a>
                                        </li>
                                        <li class="list-inline-item"><a href="#"><i class="socicon-dribbble"></i></a>
                                        </li>--}}
                                    </ul>
                                </li>
<!--
                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Nikita" src="{{ asset('images1/no-picture.png') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Nikita</h5>
                                    <span>Frontend-Programmierung</span>
                                    <ul class="list-inline list-links mt-2">
                                    </ul>
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Fahad" src="{{ asset('images1/no-picture.png') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Fahad</h5>
                                    <span>Design</span>
                                    <ul class="list-inline list-links mt-2">
                                    </ul>
                                </li>

                                <li class="col-6 col-lg-4">
                                    <img alt="Foto Fahad" src="{{ asset('images1/no-picture.png') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Sanja</h5>
                                    <span>Grafik</span>
                                    <ul class="list-inline list-links mt-2">
                                    </ul>
                                </li>-->

                                <li class="col-6 col-lg-4">
                                    <img alt="Mitarbeiter gesucht" src="{{ asset('images1/no-user.png') }}" class="img-fluid rounded mb-1"  style="max-width: 150px" />
                                    <h5 class="mb-0">Du!</h5>
                                    <a href="https://www.podcast.de/jobs" target="_blank"><span>Zu unseren Stellenangeboten</span></a>
                                    <ul class="list-inline list-links mt-2">
                                    </ul>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection
