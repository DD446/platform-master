@extends('main')

@section('content')
<div id="app" data-type="home">
    <section class="bg-info text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/dashboard_bg_1.jpg') }}" class="bg-image opacity-60" />--}}

        {{--{{ $page->getFirstMedia('bg') }}--}}
        {{--{{ $media('grey') }}--}}
        {{--{{ $media }}--}}

        <div class="container">
            <div class="row justify-content-center mb-1">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('main.header_dashboard')}}</h1>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>
    <section class="bg-white space-sm">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col pb-3 text-center">
                    <div class="text-center" v-if="false">
                        <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">...</span>
                        </div>
                    </div>
                    <usage></usage>
                    <h1 class="mb-0 pb-3">@lang('main.storage_space')</h1>
                </div>
                <!--end of col-->
                <div class="col-auto">
                    <listeners
                        title="@lang('main.title_listeners')"
                        text="@lang('main.listeners')"
                        loader="{{ asset('images1/loader_bars.svg') }}"
                        action="/api/stats/counter?stat=listeners&range=yesterday"></listeners>
{{--                    <downloads
                        title="@lang('main.title_downloads')"
                        text="@lang('main.downloads')"
                        loader="{{ asset('images1/loader_audio.svg') }}"
                        action="/api/stats/downloads/counter"></downloads>--}}
                    <subscribers
                        title="@lang('main.title_subscribers')"
                        text="@lang('main.subscribers')"
                        loader="{{ asset('images1/loader_hearts.svg') }}"
                        action="/api/stats/counter?stat=subscribers&range=last7days"></subscribers>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section class="space-sm">
        <div class="container" id="news-container">

            <div class="row justify-content-between">
                <div class="col-12 col-md-8">
                    <b-card no-body>
                        <b-tabs card>
                            <b-tab active>
                                <template v-slot:title>
                                    <i class="icon-news mr-1"></i> @lang('main.header_news')
                                </template>
                                <b-card-text>

                                    <ul class="list-group list-group-flush list-group-comments">
                                        @foreach($news as $story)
                                        <li class="list-group-item py-4">
                                            <div class="media">
                                                <div class="media-body">
                                                    <div class="mb-2">
                                                        <small class="text-muted right">
                                                            {{ $story->author }} {{\Carbon\Carbon::createFromTimeStamp(strtotime($story->created_at))->diffForHumans()}}
                                                        </small>
                                                        <span class="h5 mb-0 font-weight-bolder">{{ $story->title }}</span>
                                                        <p class="mt-2 font-weight-bold">{{ $story->teaser }}</p>
                                                    </div>
                                                    <div>
                                                        {!! nl2br($story->body) !!}
                                                    </div>

                                                    <br><br>

                                                    <like-button :likes="{{ $story->likes }}" :id="{{ $story->id }}" url="/news" feedback-type="10"></like-button>
                                                    <like-button :likes="{{ $story->dislikes }}" :id="{{ $story->id }}" url="/news" type="dislike" feedback-type="10"></like-button>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach

                                        <li class="list-group-item py-4">
                                            <a href="{{ route('news.index') }}"
                                               class="right">{{ trans('news.link_all_news') }} &rsaquo;</a>
                                        </li>
                                    </ul>

                                </b-card-text>
                            </b-tab>
{{--
                            <b-tab>
                                <template v-slot:title>
                                    <i class="icon-megaphone mr-1"></i> @lang('main.header_offers')
                                </template>
                                <b-card-text>

                                    <ul class="list-group list-group-flush list-group-comments">
                                        @foreach($offers as $offer)
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <div class="mb-2">
                                                            <span class="h5 mb-0 font-weight-bolder">{{ $offer['title'] }}</span>
                                                        </div>
                                                        <div>
                                                            {!! nl2br($offer['body']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                </b-card-text>
                            </b-tab>
--}}
                            <b-tab>
                                <template v-slot:title>
                                    <i class="icon-lab-flask mr-1"></i> @lang('main.header_changes')
                                    <b-badge variant="primary">1</b-badge>
                                </template>
                                <b-card-text>
                                    <changes></changes>
                                </b-card-text>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <h5 class="h6">@lang('main.account_information')</h5>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>@lang('main.email')</div>
                                    <span class="overflow-hidden">
                                        <a href="/email" title="{{ trans('main.text_title_email') }}"><div class="ml-4 text-nowrap">{{ auth()->user()->email }}</div></a>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>@lang('main.package')</div>
                                    <div>
                                        <a href="{{ route('packages') }}" title="{{ trans('main.text_title_package') }}">{{ trans_choice('package.package_name', auth()->user()->package->package_name) }}</a> @if(auth()->user()->isInTrial()) @lang('main.trial') @endif
                                        @if(auth()->user()->new_package_id)
                                            @php
                                                $newPackage = \App\Models\Package::find(auth()->user()->new_package_id);
                                            @endphp
                                            (<span title="{{ trans('main.package_downgrade_saved', ['name' =>
            trans_choice('package.package_name', $newPackage->package_name)]) }}"><i class="icon-arrow-down"></i> {{trans_choice('package.package_name', $newPackage->package_name)}}</span>)
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>@lang('main.funds')</div>
                                    <span>
                                        @if(auth()->user()->isInTrial())
                                            <a href="{{route('accounting.create')}}">@lang('main.end_trial')</a>
                                        @else
                                            <a href="{{route('accounting.create')}}">
                                                <div class="text-center" v-if="false">
                                                    <div class="spinner-grow" role="status">
                                                        <span class="sr-only">@lang('package.text_loading')</span>
                                                    </div>
                                                </div>
                                                <funds :amount="{{ auth()->user()->funds }}"></funds>
                                            </a>
                                        @endif
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    @if(isset($o['renewTime']))
                                        <div>@lang('main.next_billing_time')</div>
                                        <span>
                                            {{ $o['renewTime'] ?? '' }}
                                        </span>
                                    @else
                                        <div>
                                            @lang('main.billing_status')
                                        </div>
                                        @if(isset($o['trialEnded']))
                                            <a href="{{route('accounting.create')}}" class="text-danger" title="@lang('main.title_add_funds')">
                                                @lang('main.trial_finished')
                                            </a>
                                            {{--<i class="icon icon-info-with-circle text-blue" data-toggle="tooltip" data-placement="top" data-html="false" title="{{ trans('main.hint_trial_finished') }}"></i>--}}
                                        @else
                                            <span class="text-success">
                                                {{ trans('main.trial_ends', ['end' => $o['dateInterval']]) }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>Zusatzoptionen</div>
                                    <div class="ml-3">
                                        @if(auth()->user()->isInTrial())
                                            <span class="text-black-50">@lang('main.booking_hint_trial')</span>
                                        @else
                                            @forelse($o['aExtraBookings'] as $key => $value)
                                                {{ $value->extras_description }}
                                                @if(!$value->is_repeating)
                                                    @if(!in_array($value->extras_type, [\App\Models\UserExtra::EXTRA_STORAGE, \App\Models\UserExtra::EXTRA_STATSEXPORT]))
                                                    @lang('main.option_end_date', ['date' => $value->date_end->formatLocalized('%d.%m.%Y')])
                                                    @endif
                                                @endif
                                                @if(!$loop->last), @endif
                                                @if($loop->last)
                                                    <br>
                                                    <a class="" href="{{route('extras.index')}}">@lang('main.link_options')</a>
                                                @endif
                                            @empty
                                                <a class="" href="{{route('extras.index')}}">@lang('main.link_no_options')</a>
                                            @endforelse
                                        @endif
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <h5 class="h6">@lang('main.shortcut_links')</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-spacing-sm">

                                @if(!$hideReviewButton)
                                    <li>
                                        <a href="{{ route('review.create') }}" class="btn btn-primary btn-block">Deine Meinung zählt!</a>
                                    </li>
                                @endif

                                <li>
                                    <a class="btn btn-light btn-block"
                                       href="{{ route('feeds') }}">@lang('main.link_channels')</a>
                                </li>

                                <li>
                                    <a class="btn btn-light btn-block"
                                       href="{{ route('show.wizard') }}">@lang('main.link_create_show')</a>
                                </li>

                                <li>
                                    <a class="btn btn-light btn-block" href="{{route('accounting.create')}}">@lang('main.link_pay')</a>
                                </li>

                                <li>
                                    <a class="btn btn-light btn-block" href="{{route('rechnung.index')}}">@lang('main.link_bills')</a>
                                </li>

                                <li>
                                    <a class="btn btn-light btn-block"
                                       href="{{route('extras.index')}}">@lang('main.link_book_option')</a>
                                </li>

                                <li>
                                    <a class="btn btn-light btn-block"
                                       href="{{ route('teams') }}">@lang('main.link_teams')</a>
                                </li>

                                <li>
                                    <a class="btn btn-info btn-block"
                                       href="{{ route('contactus.create') }}?enquiry_type=support">@lang('main.link_support')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
</div>
@endsection
