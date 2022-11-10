@extends('main')

@section('content')
<section class="space-lg bg-dark overflow-hidden">
    {{--<img alt="Image" src="{{ asset('images1/startscreen_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
    {{--{{ $page->getFirstMedia('bg') }}--}}
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 mb-5 mb-md-0 position-relative">
                <h1 class="display-4">{{ trans('main.header_welcome') }}</h1>
                <p class="lead">
                    {{ trans('main.lead_welcome') }}
                </p>
                <a href="{{ route('packages') }}" class="btn btn-lg btn-success">{{ trans('main.button_packages') }}</a>
                <a href="{{ route('lp.tour') }}" class="btn btn-lg btn-link text-white">{{ trans('main.link_tour') }} <i class="icon-chevron-right"></i></a>
            </div>
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
<section>
    <div class="container">
        <div class="row justify-content-center text-center section-intro">
            <div class="col-12 col-md-9 col-lg-8">
                <span class="title-decorative">{{ trans('main.header_meet') }}</span>
                <h2 class="display-4">{{ trans('main.subheader_meet') }}</h2>
                <span class="lead">{{ trans('main.slogan_meet') }}</span>

            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <ul class="row feature-list feature-list-sm">
            <li class="col-12 col-md-4">
                <picture>
                    <source srcset="/images1/home/startscreen_podcast-hosting_1.avif" type="image/avif">
                    <source srcset="/images1/home/startscreen_podcast-hosting_1.webp" type="image/webp">
                    <source srcset="/images1/startscreen_hosting_1.jpg" type="image/jpeg">
                    <img
                        alt="{{ trans('main.features_img_hosting') }}"
                        src="{{ asset('images1/startscreen_hosting_1.jpg') }}"
                        height="200" width="354"
                        class="img-fluid rounded" />
                </picture>
                <h5 class="pt-2">{{ trans('main.features_header_hosting') }}</h5>
                <p>
                    {{ trans('main.features_text_hosting') }}
                </p>
                {{--<a href="{{ route('lp.host') }}">{{ trans('main.features_link_hosting') }} &rsaquo;</a>--}}
            </li>
            <!--end of col-->
            <li class="col-12 col-md-4">
                {{--<a href="{{ route('lp.socialmedia') }}" class="card">--}}
                    <picture>
                        <source srcset="/images1/home/startscreen_podcast-socialmedia_1.avif" type="image/avif">
                        <source srcset="/images1/home/startscreen_podcast-socialmedia_1.webp" type="image/webp">
                        <source srcset="/images1/startscreen_socialmedia_1.jpg" type="image/jpeg">
                            <img
                                alt="{{ trans('main.features_img_socialmedia') }}"
                                src="{{ asset('images1/startscreen_socialmedia_1.jpg') }}"
                                height="200" width="354"
                                class="img-fluid rounded" />
                    </picture>
                {{--</a>--}}
                <h5 class="pt-2">{{ trans('main.features_header_socialmedia') }}</h5>
                <p>
                    {{ trans('main.features_text_socialmedia') }}
                </p>
                {{--<a href="{{ route('lp.socialmedia') }}">{{ trans('main.features_link_socialmedia') }} &rsaquo;</a>--}}
            </li>
            <!--end of col-->
            <li class="col-12 col-md-4">
                {{--<a href="{{ route('lp.blog') }}" class="card">--}}
                <picture>
                    <source srcset="/images1/home/startscreen_podcast-blog_1.avif" type="image/avif">
                    <source srcset="/images1/home/startscreen_podcast-blog_1.webp" type="image/webp">
                    <source srcset="/images1/startscreen_blog_1.jpg" type="image/jpeg">
                        <img
                            alt="{{ trans('main.features_img_blog') }}"
                            src="{{ asset('images1/startscreen_blog_1.jpg') }}"
                            class="img-fluid rounded"
                            height="200" width="354" />
                </picture>
                {{--</a>--}}
                <h5 class="pt-2">{{ trans('main.features_header_blog') }}</h5>
                <p>
                    {{ trans('main.features_text_blog') }}
                </p>
                {{--<a href="{{ route('lp.blog') }}">{{ trans('main.features_link_blog') }} &rsaquo;</a>--}}
            </li>
            <!--end of col-->
        </ul>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->


<section class="bg-white">
    <div class="container">
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-6 col-lg-5 text-center text-md-left section-intro">
                <span class="title-decorative">@lang('main.title_podcaster_storage')</span>
                <h3 class="h1">@lang('main.header_podcaster_storage')</h3>
                <span class="lead">@lang('main.text_podcaster_storage')</span>
                {{--<a href="#">Explore Documentation &rsaquo;</a>--}}
            </div>
            <!--end of col-->
            <div class="col-8 col-md-6 col-lg-4">
                <picture>
                    <source srcset="/images1/home/startscreen_podcast-storage_1.avif" type="image/avif">
                    <source srcset="/images1/home/startscreen_podcast-storage_1.webp" type="image/webp">
                    <source srcset="/images1/startscreen_storage_1.jpg" type="image/jpeg">
                    <img
                        alt="Image"
                        src="{{ asset('images1/startscreen_storage_1.jpg') }}"
                        class="img-fluid box-shadow"
                        height="534" width="356"
                        loading="lazy" />
                </picture>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

<!--end of section-->
<section class="bg-white flush-with-above pb-5">
    <div class="container">
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-5 order-md-2 mb-5 mb-md-0">
                <span class="title-decorative">@lang('main.title_packages')</span>
                <h5 class="h1">@lang('main.header_packages')</h5>
                <p class="lead">
                    @lang('main.text_packages')
                </p>
                {{--<a href="{{ route('packages') }}">@lang('main.link_packages') &rsaquo;</a>--}}
            </div>
            <!--end of col-->
            <div class="col-12 col-md-5 order-md-1">
                <a href="{{ route('packages') }}">
                    <picture>
                        <source srcset="/images1/home/startscreen_podcast-packages_1.webp" type="image/webp">
                        <source srcset="/images1/home/startscreen_podcast-packages_1.avif" type="image/avif">
                        <source srcset="/images1/startscreen_packages_1.jpg" type="image/jpeg">
                        <img
                            alt="Podcast Hosting Pakete"
                            src="{{ asset('images1/startscreen_packages_1.jpg') }}"
                            height="631" width="451"
                            class="img-fluid box-shadow" loading="lazy" />
                    </picture>
                </a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

<?php
$review = array_shift($reviews);
?>
@isset($review)
<section class="bg-gradient text-white space-lg">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span class="title-decorative">Kundenmeinung</span>
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-lg-10">
                <div class="media">

                    <img alt="{{ $review['podcast'] }}" src="{{ asset('storage/reviews/images/' . $review['logo']) }}" class="avatar avatar-square avatar-lg" loading="lazy" />
                    <div class="media-body">
                        <blockquote class="h2">{!! $review['cite'] !!}</blockquote>
                        <span>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</span>
                    </div>
                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
@endisset

<section class="bg-white">
    <div class="container">
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-6 col-lg-5 text-center text-md-left section-intro">
                <span class="title-decorative">@lang('main.title_statistics')</span>
                <h3 class="h1">@lang('main.header_statistics')</h3>
                <span class="lead">@lang('main.text_statistics')</span>
                {{--<a href="#">Explore Documentation &rsaquo;</a>--}}
            </div>
            <!--end of col-->
            <div class="col-8 col-md-6 col-lg-4">
                <picture>
                    <source srcset="/images1/home/startscreen_podcast-statistics_1.webp" type="image/webp">
                    <source srcset="/images1/home/startscreen_podcast-statistics_1.avif" type="image/avif">
                    <source srcset="/images1/startscreen_packages_1.jpg" type="image/jpeg">
                    <img
                        alt="Vielseitige und günstige Podcast Statistiken"
                        src="{{ asset('images1/startscreen_statistics_1.jpg') }}"
                        class="img-fluid box-shadow"
                        height="534" width="356"
                        loading="lazy" />
                </picture>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
<section class="bg-white pb-5">
    <div class="container">
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-5 order-md-2 mb-5 mb-md-0">
                <span class="title-decorative">@lang('main.title_support')</span>
                <h5 class="h1">@lang('main.header_support')</h5>
                <p class="lead">
                    {!! trans('main.text_support') !!}
                </p>
                {{--<a href="{{ route('packages') }}">@lang('main.link_packages') &rsaquo;</a>--}}
            </div>
            <!--end of col-->
            <div class="col-12 col-md-5 order-md-1">
                <a href="{{ route('packages') }}">
                    <picture>
                        <source srcset="/images1/home/startscreen_podcast-support_1.webp" type="image/webp">
                        <source srcset="/images1/home/startscreen_podcast-support_1.avif" type="image/avif">
                        <source srcset="/images1/startscreen_support_1.jpg" type="image/jpeg">
                        <img
                            alt="Erstklassiger Podcast Support inkl. Telefon- und E-Mail-Support"
                            src="{{ asset('images1/startscreen_support_1.jpg') }}"
                            class="img-fluid box-shadow"
                            height="675" width="451"
                            loading="lazy" />
                    </picture>
                </a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

<section class="bg-white">
    <div class="container">
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-6 col-lg-5 text-center text-md-left section-intro">
                <span class="title-decorative">@lang('main.title_privacy')</span>
                <h3 class="h1">@lang('main.header_privacy')</h3>
                <span class="lead">@lang('main.text_privacy')</span>
                {{--<a href="#">Explore Documentation &rsaquo;</a>--}}
            </div>
            <!--end of col-->
            <div class="col-8 col-md-6 col-lg-4">
                <picture>
                    <source srcset="/images1/home/sicheres-podcasthosting-dsgvo-konform-hosting-deutsch.webp" type="image/webp">
                    <source srcset="/images1/home/sicheres-podcasthosting-dsgvo-konform-hosting-deutsch.avif" type="image/avif">
                    <source srcset="/images1/home/sicheres-podcasthosting-dsgvo-konform-hosting-deutsch.jpg" type="image/jpeg">
                    <img
                        alt="{{ trans('main.alt_privacy') }}"
                        src="{{ asset('images1/home/sicheres-podcasthosting-dsgvo-konform-hosting-deutsch.jpg') }}"
                        class="img-fluid box-shadow"
                        height="512" width="512"
                        loading="lazy" />
                </picture>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>

<?php
$review = array_shift($reviews);
?>
@isset($review)
<section class="bg-gradient text-white space-lg">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span class="title-decorative">Kundenmeinung</span>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="media">
                    <img alt="{{ $review['podcast'] }}" src="/storage/reviews/images/{{ $review['logo'] }}" class="avatar avatar-square avatar-lg" loading="lazy" />
                    <div class="media-body">
                        <blockquote class="h2">{!! $review['cite'] !!}</blockquote>
                        <span>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</span>
                    </div>
                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
@endisset

<section>
    <div class="container">
        <div class="row justify-content-center text-center section-intro">
            <div class="col-12 col-md-9 col-lg-8">
                <span class="title-decorative">@lang('main.title_summary')</span>
                <h2 class="display-4">@lang('main.header_summary')</h2>
                <span class="lead">@lang('main.subtitle_summary')</span>

            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="row justify-content-around align-items-center">
            <div class="col-12 col-md-6 col-lg-5 order-md-2">
                <ul class="nav" id="myTab" role="tablist">
                    @foreach(['payment', 'features', 'cancelation'] as $entry)
                    <li>
                        <div class="{{ $loop->first ? 'active' : '' }} card" id="tab-{{ $loop->iteration }}" data-toggle="tab" href="#{{ $entry }}" role="tab" aria-controls="{{ $entry }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="card-body">
                                <h5>@choice('main.header_extras', $entry)</h5>
                                <p>
                                    @choice('main.text_extras', $entry)
                                </p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            <!--end of col-->
            <div class="col-12 col-md-6 order-md-1">
                <div class="tab-content" id="myTabContent">
                    @foreach(['payment', 'cancelation', 'features'] as $entry)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $entry }}" role="tabpanel" aria-labelledby="tab-{{ $loop->iteration }}">
                        <picture>
                            <source srcset="/images1/home/startscreen_podcast-payment_1.webp" type="image/webp">
                            <source srcset="/images1/home/startscreen_podcast-payment_1.avif" type="image/avif">
                            <source srcset="/images1/startscreen_payment_1.jpg" type="image/jpeg">
                            <img
                                alt="@choice('main.header_extras', $entry)"
                                class="img-fluid box-shadow"
                                height="364" width="546"
                                src="{{ asset('images1/startscreen_payment_1.jpg') }}"
                                loading="lazy" />
                        </picture>
                    </div>
                    @endforeach
                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>

<section class="bg-white">
    <div class="row justify-content-center text-center section-outro bg-white">
        <div class="col-lg-8 col-md-5">
            <h4>Führende Organisationen vertrauen podcaster</h4>

            <div class="row">
                <div class="col-12 text-center">
                    <ul class="list-inline list-inline-large">
                        <li class="list-inline-item">
                            <img alt="Diakonie" class="logo logo-lg" src="{{ asset('images1/referenzen/diakonie-logo.png') }}" loading="lazy" height="45" width="245" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Deutscher Jugendherbergsverband" class="logo logo-lg" src="{{ asset('images1/referenzen/djh-logo.png') }}" loading="lazy" height="45" width="85" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-0">
                            <img alt="Greenpeace" class="logo logo-lg" src="{{ asset('images1/referenzen/greenpeace-logo.png') }}" loading="lazy" height="45" width="291" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-3">
                            <img alt="Klassikradio" class="logo logo-lg" src="{{ asset('images1/referenzen/klassikradio-logo.png') }}" loading="lazy" height="45" width="120" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-3">
                            <img alt="Deutsche Börse" class="logo logo-lg" src="{{ asset('images1/referenzen/deutsche-boerse-gruppe-logo.png') }}" loading="lazy" height="67" width="140" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-3">
                            <img alt="Epson" class="logo logo-lg" src="{{ asset('images1/referenzen/epson-logo.png') }}" loading="lazy"  height="45" width="110" />
                        </li>
                        <li class="list-inline-item mt-2 mt-sm-4 mt-md-3">
                            <img alt="Pernot Ricard" class="logo logo-lg" src="{{ asset('images1/referenzen/pernot-ricard-logo.png') }}" loading="lazy"  height="45" width="122" />
                        </li>
                    </ul>
                </div>
                <!--end of col-->
            </div>

            <div class="row mt-5">
                <div class="col-8 offset-2 text-center">
                    Mehr als 5.000 Kunden aus der DACH-Region vertrauen podcaster - von Privatpersonen über öffentliche Einrichtungen hin zu Dax-Unternehmen und Global Playern. Wir sind für unsere Kunden da, damit ihre Podcasts Gehör finden.
                </div>
            </div>
        </div>
        <!--end of col-->
    </div>
    <!--end of row-->
</section>

<section class="bg-gradient p-0 text-white">
    <svg class="decorative-divider" preserveAspectRatio="none" viewbox=" 0 0 100 100">
        <polygon fill="#F8F9FB" points="0 0 100 0 100 100"></polygon>
    </svg>
    <div class="container space-lg">
        <div class="row text-center">
            <div class="col">
                <h3 class="h1">@lang('main.header_final_call')</h3>
                <a href="{{ route('packages') }}" class="btn btn-lg btn-light">@lang('main.button_final_call')</a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->


<section class="bg-white">
    <div class="row justify-content-center text-center section-outro bg-white">
        <div class="col-lg-8 col-md-5">
            <h4>Über unsere Podcast Hosting Plattform</h4>

            <div class="row">
                <div class="col-12 text-center">
                    <p>podcaster.de zählt zu den größten Podcast Hosting Plattformen weltweit. Tausende Podcast Hosts sehen in uns den besten Podcast Hosting Anbieter.</p>
                    <p>Mit unserem Web Player auf der Podcast Seite oder einem schicken RSS Feed unseres Hosting Service wird jede Podcast Episode ganz einfach Deinen Podcast Hörern zugänglich gemacht.</p>
                    <p>Wir unterstützen Dich nicht nur dabei, Deinen eigenen Podcast zu veröffentlichen, sondern den besten Podcast in Deiner Kategorie vom Podcast Start an, zu veröffentlichen.</p>
                    <p>Wir sind nicht irgendeine Audio Plattform, wir sind podcaster.de - bei uns steckt Podcasting in der DNA.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!--end of section-->
@endsection('content')
