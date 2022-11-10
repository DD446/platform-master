@extends('main')

@section('content')
    <div class="container" id="app" data-type="fileupload">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-10">
                        <h1 class="h2 mb-2">@lang('mediamanager.header_upload')</h1>
                        <span>@lang('mediamanager.subheader_upload')</span>
                    </div>
                    <!--end of col-->
                    <div class="col-2">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                            <!--                            <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                                                           onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                                            <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                                                        </a>-->
                            <a href="https://www.podcaster.de/faq/antwort-64-wie-kann-ich-sofort-mehr-speicherplatz-erhalten"
                               class="d-none d-sm-block"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                            </a>
                        </div>
                    </div>
                </div>
                <!--end of row-->
                <div class="row">
                    <div class="col pt-4 text-center">
                        <alert-container></alert-container>
                        <div class="text-center">
                            <div class="spinner-grow m-5" role="status" v-if="false">
                                <span class="sr-only">@lang('feeds.text_loading_data')</span>
                            </div>
                        </div>
                        <usage></usage>
                        <p class="mb-0 pb-3">@lang('main.storage_space')</p>
                    </div>
                </div>
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section class="flush-with-above">
            <!--end of container-->
            @if($uploadBlocked)
                <div class="container card">
                    <div class="text-center alert-warning m-4 p-4">
                        {!! trans('main.upload_blocked_insufficient_funds', ['route' => route('accounting.create')]) !!}
                    </div>
                </div>
            @else
                <div class="container card">
                    <div class="text-center">
                        <div class="spinner-grow m-5" role="status" v-if="false">
                            <span class="sr-only">@lang('feeds.text_loading_data')</span>
                        </div>
                    </div>
                    <section class="m-1 m-md-3 mg-lg-4">
                        <fileuploader url="{{ route('media.upload.chunks') }}"></fileuploader>
                    </section>
                </div>
            @endif
        </section>
        <!--end of container-->

    </div>
@endsection
