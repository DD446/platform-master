@extends('main')

@section('content')
    <div class="container" id="app" data-type="filedownloaderdropbox">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">@lang('mediamanager.header_download_dropbox')</h1>
                        <span>@lang('mediamanager.subheader_download_dropbox')</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
                <div class="row">
                    <div class="col pt-4 text-center">
                        <alert-container></alert-container>
                        <usage></usage>
                        <p class="mb-0 pb-3">@lang('main.storage_space')</p>
                    </div>
                </div>
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section class="flush-with-above">
            <div class="text-center mt-4">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <!--end of container-->
            @if($uploadBlocked)
                <div class="container card">
                    <div class="text-center alert-warning m-4 p-4">
                        {!! trans('main.upload_blocked_insufficient_funds', ['route' => route('accounting.create')]) !!}
                    </div>
                </div>
            @else
                <div class="container card">
                    <div id="dropbox">
                        <file-downloader-dropbox></file-downloader-dropbox>
                    </div>
                </div>
            @endif
        </section>
        <!--end of container-->

    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="zcsnkhkmd6ocbwq"></script>
@endpush
