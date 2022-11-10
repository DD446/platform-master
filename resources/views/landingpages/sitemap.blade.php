@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('sitemap') }}
@endsection

@section('content')

    <section class="space-xs bg-gradient">
        <div class="container default-text main-content">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_sitemap')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_sitemap')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="space">
        <div class="container">

            <ul class="row feature-list feature-list-sm">

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.reviews') }}">
                                <h4 class="card-title">@lang('pages.page_title_reviews')</h4>
                                <p class="card-text">@lang('pages.page_description_reviews')</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.tour') }}">
                                <h4 class="card-title">@lang('pages.page_title_tour')</h4>
                                <p class="card-text">@lang('pages.page_description_tour')</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('packages') }}">
                                <h4 class="card-title">@lang('package.page_title_packages')</h4>
                                <p class="card-text">@lang('package.page_description_packages')</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('faq.index') }}">
                                <h4 class="card-title">@lang('faq.title_seo_faq')</h4>
                                <p class="card-text">@lang('faq.description_seo_faq')</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

<!--                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.host') }}">
                                <h4 class="card-title">@lang('pages.page_title_podcasthost')</h4>
                                <p class="card-text">@lang('pages.page_description_podcasthost')</p>
                            </a>
                        </div>
                    </div>
                </li>-->
                <!--end of col-->

<!--                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.hoster') }}">
                                <h4 class="card-title">@lang('pages.page_title_hoster')</h4>
                                <p class="card-text">@lang('pages.page_description_hoster')</p>
                            </a>
                        </div>
                    </div>
                </li>-->
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.videohosting') }}">
                                <h4 class="card-title">@lang('pages.page_title_videohosting')</h4>
                                <p class="card-text">@lang('pages.page_description_videohosting')</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

<!--                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.audiohosting') }}">
                                <h4 class="card-title">@lang('pages.page_title_audiohosting')</h4>
                                <p class="card-text">@lang('pages.page_description_audiohosting')</p>
                            </a>
                        </div>
                    </div>
                </li>-->
                <!--end of col-->

<!--                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.coach') }}">
                                <h4 class="card-title">@lang('pages.page_title_coach')</h4>
                                <p class="card-text">@lang('pages.page_description_coach')</p>
                            </a>
                        </div>
                    </div>
                </li>-->
                <!--end of col-->

<!--                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="#">
                                <h4 class="card-title">@lang('pages.page_title_trainer')</h4>
                                <p class="card-text">@lang('pages.page_description_trainer')</p>
                            </a>
                        </div>
                    </div>
                </li>-->
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('news.index') }}">
                                <h4 class="card-title">@lang('news.page_title')</h4>
                                <p class="card-text">{{trans('news.page_description', ['name' => config('app.name')])}}</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('page.press') }}">
                                <h4 class="card-title">@lang('pages.header_press')</h4>
                                <p class="card-text">{{trans('pages.description_press')}}</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('page.imprint') }}">
                                <h4 class="card-title">{{trans('pages.header_imprint')}}</h4>
                                <p class="card-text">{{trans('pages.page_description_imprint')}}</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('contactus.create') }}">
                                <h4 class="card-title">{{trans('contact_us.title')}}</h4>
                                <p class="card-text">{{trans('contact_us.page_description')}}</p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                @foreach(\App\Models\LandingPage::all() as $lp)
                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('lp.builder', ['lp' => $lp->id, 'slug' => $lp->slug]) }}">
                                <h4 class="card-title">{{ $lp->page_title  }}</h4>
                                <p class="card-text">{{ $lp->page_description }}</p>
                            </a>
                        </div>
                    </div>
                </li>
                @endforeach
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="https://www.podcast.de" target="_blank">
                                <h4 class="card-title">
                                    Podcast-Portal podcast.de
                                </h4>
                                <p class="card-text">
                                    In unserem Podcast-Verzeichnis kannst Du Deine Podcasts kostenlos anmelden, neue Abonnenten finden und die Reichweite steigern.
                                </p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="https://www.podcastpioniere.de" target="_blank">
                                <h4 class="card-title">
                                    Podcast-Vermarktung Podcast-Pioniere
                                </h4>
                                <p class="card-text">
                                    Über unseren Dienst Podcast Pioniere kannst Du Deinen Podcast vermarkten lassen und durch die Monetarisierung mit dynamisch platzierten Werbeanzeigen Geld verdienen.
                                </p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="https://www.podspace.de" target="_blank">
                                <h4 class="card-title">
                                    Podspace Podcast-Studio
                                </h4>
                                <p class="card-text">
                                    Im Podspace entsteht die Podcast Plattform und die Podcast-Produktionen <em>Naps</em> und <em>In 5 Minuten zu...</em> und vielleicht demnächst auch Ihr Podcast?
                                </p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->

                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <a href="https://www.podcasting.de" target="_blank">
                                <h4 class="card-title">
                                    Podcast-Agentur podcasting.de
                                </h4>
                                <p class="card-text">
                                    Ihr professioneller Ansprechpartner für Corporate Podcast-Produktionen aus Berlin und Gütersloh.
                                </p>
                            </a>
                        </div>
                    </div>
                </li>
                <!--end of col-->
            </ul>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
@endsection('content')
