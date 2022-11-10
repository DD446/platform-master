@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('stats') }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10">
                    <h1 class="h2 mb-2">@lang('stats.header')</h1>
                    <span>@lang('stats.subheader')</span>
                </div>
                <!--end of col-->
                <div class="col-2">
                    <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                        <!--                            <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                                                       onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                                        <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                                                    </a>-->
                        <a href="https://www.podcaster.de/faq/antwort-185-wie-sind-die-neuen-statistiken-zu-lesen"
                           class="d-none d-sm-block"
                           onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                            <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                        </a>
                    </div>
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <section id="app" data-type="statistics">

        <div class="text-center">
            <div class="spinner-grow m-3" role="status" v-if="false">
                <span class="sr-only">@lang('package.text_loading')</span>
            </div>
        </div>

        <stats
            @if($useNewStatistics) use-new-statistics @endif
            @if($usesAudiotakes) uses-audiotakes @endif
            @if($canExport) can-export @endif></stats>

    </section>

@endsection('content')
