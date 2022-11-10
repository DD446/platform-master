@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('feeds_channel', $feed) }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h2 class="h5 mb-2">{{$feed->rss['title']}}</h2>
                    <h1 class="h2 mb-2">@lang('feeds.header_channel')</h1>
                    <span>@lang('feeds.subheader_channel', ['title' => $feed->rss['title']])</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

<!--    <section class="height-70">
        <div class="container" id="app" data-type="feeds">
            <alert-container></alert-container>
        </div>
    </section>-->

    <section class="container bg-white">
        <div class="row">
            <div class="col-12">
                <form action="{{route('feeds')}}" method="post" class="std">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="channel[charset]" value="utf-8" />
                    <input type="hidden" name="feedId" value="{{$feedId}}" />
                    <input type="hidden" name="type" value="rss" />

                    @if($protection)
                    <input type="hidden" name="channel[settings][protection_id]" value="{{ $channel['settings']['protection_id']}}" />
                    @endif

                    @include('parts.fields.text', [
                        'autofocus' => true,
                        'required' => true,
                        'textCount' => false,
                        'help' => trans('feeds.help_title_feed'),
                        'value' => old('channel[rss][title]', $channel['rss']['title']),
                        'label' => trans('feeds.label_title_feed'),
                        'placeholder' => trans('feeds.placeholder_title_feed')
                    ])

                    @include('parts.fields.textarea', [
                        'required' => true,
                        'value' => old('channel[rss][description]', $channel['rss']['description']),
                        'label' => trans('feeds.label_description_feed'),
                        'placeholder' => trans('feeds.placeholder_description_feed'),
                        'help' => trans('feeds.help_description_feed'),
                    ])

                    @include('parts.fields.text', [
                        'type' => 'url',
                        'required' => true,
                        'help' => trans('feeds.help_link_feed'),
                        'value' => old('channel[rss][link]', $channel['rss']['link']),
                        'label' => trans('feeds.label_link_feed'),
                        'placeholder' => trans('feeds.placeholder_link_feed')
                    ])

                    @include('parts.fields.text', [
                        'required' => true,
                        'help' => trans('feeds.help_author_feed'),
                        'value' => old('channel[rss][author]', $channel['rss']['author']),
                        'label' => trans('feeds.label_author_feed'),
                        'placeholder' => trans('feeds.placeholder_author_feed')
                    ])

                    @include('parts.fields.text', [
                        'type' => 'email',
                        'required' => true,
                        'help' => trans('feeds.help_email_feed'),
                        'value' => old('channel[rss][email]', $channel['rss']['email']),
                        'label' => trans('feeds.label_email_feed'),
                        'placeholder' => trans('feeds.placeholder_email_feed')
                    ])

                    @include('parts.fields.text', [
                        'required' => true,
                        'help' => trans('feeds.help_copyright_feed'),
                        'value' => old('channel[rss][copyright]', $channel['rss']['copyright']),
                        'label' => trans('feeds.label_copyright_feed'),
                        'placeholder' => trans('feeds.placeholder_copyright_feed')
                    ])

                    <div class="form-group">
                        <label for="channel_language">{{trans('LABEL: Language')}}<span class="required">*</span></label>
                        <select name="channel[rss][language]" id="channel_language" class="form-control form-control-lg" title="{{trans('TIP: Choose the channel language')}}" required>
                            <option value=""></option>
                            {generateSelect(aLangCode,channel[rss][language]):h}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="channel_category">{{trans('LABEL: Category')}}</label>
                        <input type="text" name="channel[rss][category]" value="{{ $channel['rss']['category']}}" id="channel_category" class="form-control" title="{{trans('TIP: Enter a channel category')}}" placeholder="{{trans('PLACEHOLDER: category')}}" />
                    </div>

                                <label>{{trans('LABEL: Logo')}}</label>
                                <flexy:include src="ajaxFeedLogo.html" />

                    <div id="feed_itunes">
                        <h2>iTunes</h2>
                                <fieldset>

                                    <label for="channel_subtitle">{{trans('LABEL: ITUNES: Subtitle')}}</label>
                                    <input type="text" name="channel['itunes'][subtitle]" id="channel_subtitle" value="{{ $channel['itunes']['subtitle']}}" maxlength="255" class="form-control" title="{{trans('TIP: Enter a channel subtitle')}}" />

                                    <label for="channel_itunes_explicit">{{trans('LABEL: ITUNES: Explicit')}}</label>
                                    <select name="channel['itunes'][explicit]" id="channel_itunes_explicit" title="{{trans('TIP: Explicit channel')}}">
                                        {generateSelect(aNoYes,channel['itunes'][explicit]):h}
                                    </select>

                                    <label for="channel_type">Podcast-Typ</label>
                                    <select name="channel['itunes'][type]" id="channel_type" title="Beim Typ 'Episoden' wird die neueste Folge zuerst angezeigt. Der Typ unterstützt 'Staffeln' (seasons). Beim Typ 'Fortsetzung' wird in ITunes zuerst die älteste Folge angezeigt.">
                                        {generateSelect(aItunesType,channel['itunes'][type]):h}
                                    </select>
                                </fieldset>

                                <label for="channel_itunes_category">{{trans('LABEL: ITUNES: Category')}}<span class="required">*</span></label>
                                <div class="error" flexy:if="error[itunes_category]">
                                    {tr(error[itunes_category])}
                                </div>
                                <select name="channel['itunes'][category][]" id="channel_itunes_category" required="required">
                                    <option value=""></option>
                                    {generateSelect(aItunesCategories,channel['itunes'][category][0]):h}
                                </select>
                                <select name="channel['itunes'][category][]">
                                    <option value=""></option>
                                    {generateSelect(aItunesCategories,channel['itunes'][category][1]):h}
                                </select>
                                <select name="channel['itunes'][category][]">
                                    <option value=""></option>
                                    {generateSelect(aItunesCategories,channel['itunes'][category][2]):h}
                                </select>

                            <h3>Erreichbarkeit <span class="lightfont">(Vorsicht)</span></h3>

                                <fieldset>

                                    <label for="channel_itunes_complete">
                                        <input type="checkbox" value="yes" name="channel['itunes'][complete]" id="channel_itunes_complete" flexy:raw="{isChecked(channel['itunes'][complete]):h}" />
                                        Beendet/Vollständig
                                    </label>

                                </fieldset>

                                <fieldset class="margintop">

                                    <label for="channel_block">{{trans('LABEL: ITUNES: Block')}}</label>
                                    <select name="channel['itunes'][block]" id="channel_block" title="{{trans('TIP: Block channel')}}">
                                        {generateSelect(aNoYes,channel['itunes'][block]):h}
                                    </select>

                                </fieldset>
                                <fieldset>

                                    <label for="channel_newfeedurl">{{trans('LABEL: ITUNES: New feed url')}}</label>
                                    <div class="error" flexy:if="error[new_feed_url]">
                                        {tr(error[new_feed_url])}
                                    </div>
                                    <input type="url" name="channel['itunes'][new-feed-url]" id="channel_newfeedurl" value="{{ $channel['itunes']['new-feed-url']}}" class="form-control" title="{{trans('TIP: New feed url')}}" placeholder="https://" />

                                </fieldset>
                    </div>
                    <div id="feed_googleplay">

                                <fieldset>
                                    <label for="channel_google_description">Beschreibung</label>
                                    <textarea id="channel_google_description" name="channel[googleplay][description]" title="Die Beschreibung darf bis zu 4000 Zeichen enthalten.">{channel[googleplay][description]}</textarea>
                                </fieldset>

                                <label for="channel_googleplay_author">{{trans('LABEL: Author')}}</label>
                                <div class="error" flexy:if="error[channel_googleplay_author]">
                                    {tr(error[channel_googleplay_author])}
                                </div>
                                <input type="text" name="channel[googleplay][author]" value="{{ $channel[googleplay][author]}}" id="channel_googleplay_author" class="form-control" title="Namen des Produzenten oder Namen des produzierenden Unternehmen" />

                                <fieldset>

                                    <label for="channel_googleplay_category">Kategorie</label>
                                    <div class="error" flexy:if="error[channel_googleplay_category]">
                                        {tr(error[channel_googleplay_category])}
                                    </div>
                                    <select name="channel[googleplay][category]" id="channel_googleplay_category">
                                        <option value=""></option>
                                        {generateSelect(aGooglePlayCategories,channel[googleplay][category]):h}
                                    </select>

                                    <label for="channel_google_explicit">{{trans('LABEL: ITUNES: Explicit')}}</label>
                                    <select name="channel[googleplay][explicit]" id="channel_google_explicit" title="{{trans('TIP: Explicit channel')}}">
                                        {generateSelect(aNoYes,channel[googleplay][explicit]):h}
                                    </select>

                                </fieldset>

                            <h3>Erreichbarkeit <span class="lightfont">(Vorsicht)</span></h3>

                                <fieldset>

                                    <label for="channel_googleplay_block">Abruf für Google-Crawler sperren</label>
                                    <select name="channel[googleplay][block]" id="channel_googleplay_block" title="{{trans('TIP: Block channel')}}">
                                        {generateSelect(aNoYes,channel[googleplay][block]):h}
                                    </select>

                                </fieldset>

                    </div>
                    <div id="feed_settings">
                                <fieldset>
                                    <label for="default_item_title">{{trans('LABEL: Default title for item')}}</label>
                                    <input type="text" class="form-control" name="channel[settings][default_item_title]" value="{{ $channel[settings][default_item_title]}}" id="default_item_title" title="{{trans('TIP: Set a default item title')}}" />
                                    <label for="default_item_description">{{trans('LABEL: Default description for item')}}</label>
                                    <textarea id="default_item_description" name="channel[settings][default_item_description]" title="{{trans('TIP: Set a default item description')}}">{{ $channel['settings']['default_item_description'] }}</textarea>
                                </fieldset>
                                <fieldset>
                                    <legend>{{trans('Hooks')}}</legend>
                                    <label for="ping_portal"><input type="checkbox" class="checkbox" name="channel[settings][ping]" value="1" id="ping_portal" flexy:raw="{isChecked(channel[settings][ping]):h}" title="{{trans('TIP: Ping portal')}}" /> {{trans('LABEL: Ping portal')}}</label>
                                    {if:channel[settings][protection]}
                                    {if:protection}
                                    <label for="protect_feed"><input type="checkbox" class="checkbox" name="channel[settings][protection]" value="1" id="protect_feed" flexy:raw="{isChecked(channel[settings][protection]):h}" title="{{trans('TIP: Protect feed')}}" /> {{trans('LABEL: Protect feed')}}</label>
                                    {else:}
                                    <label for="protect_feed"><input type="checkbox" class="checkbox" name="" value="" id="protect_feed" readonly="readonly" disabled="disabled" /> {{trans('LABEL: Protect feed')}} <span class="lightfont">(Verfügbar ab <a href="{makeUrl(#packages#,#hosting#,#podcaster')}}" title="Wechsle jetzt in ein größeres Paket!">Paket Profi</a>)</span></label>
                                    {end:}
                                    {end:}
                                    <label for="add_download_link">
                                        <input type="checkbox" class="checkbox" name="channel[settings][downloadlink]" value="1" id="add_download_link" flexy:raw="{isChecked(channel[settings][downloadlink]):h}" title="{{trans('TIP: Add download link')}}" /> {{trans('LABEL: Add download link')}}
                                    </label>
                                    <textarea name="channel[settings][downloadlink_description]" id="download_link_description" style="display: none;" title="{{trans('TIP: Download link description')}}">{channel[settings][downloadlink_description]}</textarea>
                                    <label for="deactivate_comments"><input type="checkbox" class="checkbox" name="channel[settings][inactiveComments]" value="1" id="deactivate_comments" flexy:raw="{isChecked(channel[settings][inactiveComments]):h}" title="{{trans('TIP: Deactivate blog comments')}}" /> {{trans('LABEL: Deactivate blog comments')}}</label>
                                </fieldset>
                                <fieldset title="{{trans('Approvals')}}">
                                    <legend>{{trans('Approvals')}}</legend>
                                    {if:aApprovals}
                                    {foreach:aApprovals,key,value}
                                    <label for="approvals-{value.service}-{value.screen_name}"><input type="checkbox" class="checkbox" name="channel[settings][approvals][{value.service}][]" value="{{value.screen_name}}" id="approvals-{value.service}-{value.screen_name}" flexy:raw="{isChecked(value.checked):h}" title="{{trans('TIP: Add approval')}}" /> <span class="service_{value.service}" title="{tr(value.service)}">{value.screen_name}</span></label>
                                    {end:}
                                    {end:}
                                    <p class="std padleft10 padtop10">
                                        <a href="{makeUrl(#list#,#oauth#,#podcaster')}}" class="serveraddicon">{{trans('Add approval')}}</a>
                                    </p>
                                    <br><br>
                                    <label for="channel_settings_ads">
                                        <input type="checkbox" class="checkbox" name="channel[settings][ads]" value="1" flexy:raw="{isChecked(channel[settings][ads]):h}" title="{{trans('TIP: Interested in ads')}}" id="channel_settings_ads" /> {{trans('LABEL: Interested in ads')}}
                                    </label>
                                </fieldset>
                                <fieldset>
                                    <legend>{{trans('Other settings')}}</legend>
                                    <label for="feed_entries">{{trans('Number of entries in feed')}}</label>
                                    <select name="channel[settings][feed_entries]" id="feed_entries" title="{{trans('TITLE: Number of entries in feed')}}">
                                        {generateSelect(numberOfEntriesInFeed,channel[settings][feed_entries]):h}
                                    </select>
                                </fieldset>
                            <fieldset>

                        <input type="submit" name="submitted" class="btn btn-primary" value="{{trans('BUTTON: Save changes')}}" />
                        <a href="{{route('feeds')}}" class="cancel">{{trans('Cancel')}}</a>
                </form>
            </div>
        </div>
    </section>

@endsection('content')

{{--
@push('scripts')
    <script src="{{ mix('js1/feeds.js') }}"></script>
@endpush--}}
