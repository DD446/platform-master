@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('package', $package) }}
@endsection

@section('content')
<section>
    <div class="container">
        <div class="row flex-md-row card card-lg">
            <div class="col-12 col-md-7 card-body bg-secondary">
                <div class="row mb-5 d-none d-md-block">
                    <div class="col text-center">
                        <img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}" />
                    </div>
                    <!--end of col-->
                </div>
                <div class="text-center mb-5">
                    <h1 class="h2 mb-2">@lang('package.header_main', ['name' => $packageName])</h1>
                    <span>@lang('package.text_main')</span>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9" id="app" data-type="order">
                        <div class="text-center" v-if="false">
                            <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">...</span>
                            </div>
                        </div>
                        <order
                            terms-url="{{ route('page.terms') }}"
                            privacy="{{ route('page.privacy') }}"
                            id="{{ $package->package_id }}"></order>
                    </div>
                </div>
            </div>
            <!--end of col-->
            <div class="col-12 col-md-5 card-body">
                <div>
                    <div class="mb-5 text-center">
                        <h3 class="h2 mb-2">@lang('package.header_benefits')</h3>
                        <span>@lang('package.subtitle_benefits')</span>
                    </div>

                    <ul class="list-unstyled list-spacing-sm mb-5 ">
                        <li class="row">
                            <div class="col-2 col-md-1"><i class="icon-check text-green h5"></i></div>
                            <div class="col-10">
                                {{ $package->monthly_cost }}&euro; monatlich
                            </div>
                        </li>
                        <li class="row">
                            <div class="col-2 col-md-1"><i class="icon-check text-green h5"></i></div>
                            <div class="col-10">
                                {{ trans_choice('package.paying_rhythm', $package->paying_rhythm) }}
                            </div>
                        </li>
                        @foreach($benefits as $name => $package)
                            @if($package['status'] > 0)
                                <li class="row">
                                    <div class="col-2 col-md-1"><i class="icon-check text-green h5"></i></div>
                                    <div class="col-10">
                                        @if(is_numeric($package['units']))
                                            @if($name == 'statistics')
                                                {{ trans_choice('package.statistics_name', $package['units']) }}
                                            @elseif($name == 'protection_user')
                                                {{ trans('package.feature_protection_user', ['count' => $package['units']]) }}
                                            @elseif($package['units'] === 1)
                                                1
                                            @elseif($package['units'] > 0)
                                                @lang('package.feature_units', ['count' => $package['units']])
                                            @endif
                                        @else
                                            {{ $package['units'] }}
                                        @endif

                                        @switch($name)
                                            @case('statistics')
                                            @case('support')
                                                @break
                                            @case('blogs')
                                                {{ trans_choice('package.feature_blog', $package['units']) }}
                                                @break
                                            @case('feeds')
                                                {{ trans_choice('package.feature_feed', $package['units']) }}
                                                @break
                                            @case('domains')
                                                {{ trans_choice('package.feature_domain', $package['units']) }}
                                                @break
                                            @case('subdomains')
                                                {{ trans_choice('package.feature_subdomain', $package['units']) }}
                                                @break
                                            @case('subdomains_premium')
                                                {{ trans_choice('package.feature_subdomain_premium', $package['units']) }}
                                                @break
                                            @default
                                                {{ trans_choice('package.feature_name', $name) }}
                                        @endswitch
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    @if($review > 0)
                    <div class="card card-sm box-shadow text-left">
                        <div class="card-body p-4">
                            <div class="media">
                                <img alt="{{ $review['podcast'] }}" src="{{ asset('storage/reviews/images/' . $review['logo']) }}" class="avatar avatar-square avatar-sm" />
                                <div class="media-body">
                                    <blockquote class="mb-1 text-small">
                                        {!! $review['cite'] !!}
                                    </blockquote>
                                    <small>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="text-center">
            <span class="text-small">@lang('package.hint_existing_account') <a href="{{ route('login') }}">@lang('package.link_existing_account')</a>
            </span>
        </div>
    </div>
</section>
@endsection
