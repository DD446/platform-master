@extends('main')

@section('content')
    <div class="container">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-10">
                        <h1 class="h2 mb-2">@lang('mediamanager.header_upload_replace')</h1>
                        <span>@lang('mediamanager.subheader_upload_replace', ['filename' => $file['name']])</span>
                    </div>
                    <!--end of col-->
                    <div class="col-2">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
<!--                            <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                            </a>-->
                            <a href="https://www.podcaster.de/faq/antwort-139-ich-muss-die-mp3-datei-einer-episode-austauschen-wie-mache-ich-das"
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

        <section class="flush-with-above">
            <!--end of container-->
            <div class="container p-4 card" id="app" data-type="replace">
                <fileuploader
                    no-chunking
                    maxfiles=1
                    url="{{ route('media.replace', ['id' => $id]) }}"></fileuploader>
            </div>
        </section>
        <!--end of container-->

    </div>
@endsection
