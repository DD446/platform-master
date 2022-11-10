@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('mediamanager') }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10">
                    <h1 class="h2 mb-2">@lang('mediamanager.header')</h1>
                    <span>@lang('mediamanager.subheader')</span>
                </div>
                <!--end of col-->
<!--                <div class="col-2">
                    <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                        <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                           onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                            <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                        </a>
                        <a href="https://www.podcaster.de/faq/antwort-89-wie-veroeffentliche-ich-meinen-podcast-auf-podcast-portalen-und-streamingdiensten"
                           class="d-none d-sm-block"
                           onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                            <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                        </a>
                    </div>
                </div>-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

    <div class="container">

        <section class="">
            <div class="row pb-4">
                <div class="col-12 float-right">
                    <div class="float-right">
                        <a href="{{ route('media.upload') }}" class="btn btn-primary" onclick="window.open(this.href, 'uploader','width=1000,height=850,top=15,left=15,scrollbars=yes');return false;">Datei(en) hochladen</a>
                        {{--<a href="/podcasts/upload/popup" class="btn btn-outline-primary" onclick="window.open(this.href, 'uploader','width=1000,height=850,top=15,left=15,scrollbars=yes');return false;">Datei(en) hochladen (alter Uploader)</a>--}}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <!--end of container-->
            <div class="container p-4 card" id="app" data-type="media">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <div class="spinner-grow m-3" role="status" v-if="false">
                                <span class="sr-only">@lang('package.text_loading')</span>
                            </div>
                        </div>
                        <alert-container></alert-container>
                        <mediatable baseUrl="{{ route('mediathek.index') }}" ></mediatable>
                    </div>
                </div>
            </div>
        </section>
        <!--end of container-->
    </div>
@endsection
