@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('package_extras') }}
@endsection

@section('content')
    <section class="bg-info text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/faq_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
        {{--{{ $page->getFirstMedia('bg') }}--}}

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('package.header_extras')}}</h1>
                    <span class="title-decorative">{{trans('package.subheader_extras')}}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <!--end of section-->
    <section class="">
        <div class="container" id="app" data-type="packageextras">
            <div class="text-center">
                <div class="spinner-grow m-3" role="status" v-if="false">
                    <span class="sr-only">@lang('package.text_loading')</span>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <alert-container></alert-container>
                </div>
            </div>
            @if(!$isTrial)
            <package-extras :bookable="{{ json_encode(\App\Models\UserExtra::getBookable()) }}"
                            {{ $canAddPlayerConfiguration ? ' can-add-player-configuration' : ''}}
                            {{$canAddMember ? ' can-add-member' : ''}}
                            {{$canAddStatsExport ? ' can-add-stats-export' : ''}}></package-extras>
            @else
                <div class="text-center alert-warning m-4 p-4">
                    @lang('package.text_warning_no_extras_available_during_trial', ['routePackages' => route('packages')])
                </div>
            @endif
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
