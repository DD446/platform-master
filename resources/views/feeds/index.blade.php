@extends('main')

@section('content')

    <div id="app" data-type="feeds">

        <section v-if="false">
            <div class="container-fluid">
                <div class="text-center">
                    <div class="spinner-grow m-3" role="status">
                        <span class="sr-only">@lang('package.text_loading')</span>
                    </div>
                </div>
            </div>
        </section>

        <channels
            logo-upload-url="{{ route('feed.logo') }}"
            player-base-url="{{ config('app.url') }}"
            username="{{ auth()->user()->username }}"
            {{ $canEmbed ? ' can-embed ' : '' }}
            {{ $canSchedulePosts ? ' can-schedule-posts ' : '' }}
            {{ $hasAuphonic ? ' has-auphonic ' : '' }}
            {{ $canCreatePlayerConfigurations ? ' can-create-player-configurations ' : '' }}
            {{ $canUseCustomPlayerStyles ? ' can-use-custom-player-styles ' : '' }}
            amount-player-configurations="{{ $amountPlayerConfigurations}}"
            :custom-domain-feature="{{ json_encode($customDomainFeature) }}"
            uuid="{{user_uuid(auth()->user())}}"></channels>

<!--        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">@lang('feeds.header') <channel-count username="{{ auth()->user()->username }}"></channel-count></h1>
                        <span>@lang('feeds.subheader')</span>
                    </div>
                    &lt;!&ndash;end of col&ndash;&gt;
                </div>
                &lt;!&ndash;end of row&ndash;&gt;
            </div>
            &lt;!&ndash;end of container&ndash;&gt;
        </section>
        &lt;!&ndash;end of section&ndash;&gt;

        <section>
            <div class="container-fluid">
                <div class="text-center">
                    <div class="spinner-grow m-3" role="status" v-if="false">
                        <span class="sr-only">@lang('package.text_loading')</span>
                    </div>
                </div>
                <alert-container></alert-container>
                <channel-list
                    logo-upload-url="{{ route('feed.logo') }}"
                    username="{{ auth()->user()->username }}"
                    can-embed="{{ $canEmbed }}"
                    :custom-domain-feature="{{ json_encode($customDomainFeature) }}"
                    has-auphonic="{{ $hasAuphonic }}"></channel-list>
            </div>
        </section>
-->
<!--        @if($hasFeeds)
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-3">
                        <a href="{{ route('podcast.wizard') }}" class="btn btn-outline-primary"
                           title="Hier kannst du einen neuen Podcast(-Feed/-Kanal) erstellen oder einen bestehenden Podcast importieren.">
                            <i class="icon-add-to-list display-4 opacity-20"></i>
                            {{ trans('feeds.link_create_feed') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif-->
    </div>
@endsection('content')
