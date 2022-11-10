<section class="bg-info text-light">
    <div class="container-fluid text-center">
        <div class="row justify-content-center">
            <div class="col">
                <span class="title-decorative">@lang('package.subtitle_guest')</span>
                <h2 class="display-4">@lang('package.header_guest')</h2>
                <span class="lead">@lang('package.lead_guest')</span>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

<section>
    <div class="container-fluid">
        <!--end of row-->
        <div class="row justify-content-center">
            <div class="col">
                <table class="table table-bordered table-responsive pricing" id="app" data-type="packages">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            @foreach($aPackages as $oPackage)
                            <th scope="col">
                                <h5>{{ trans_choice('package.package_name', $oPackage->package_name) }}</h5>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row" class="bg-secondary">
                            @lang('package.header_price')
                            {{--<br><small>Deutschland, 19% MwSt</small>--}}
                        </th>
                        @foreach($aPackages as $oPackage)
                        <td class="bg-light">
                            <span class="display-4">{{ $oPackage->monthly_cost }}&euro;</span>
                            <span class="text-small" title="{{ trans_choice('package.paying_rhythm', $oPackage->paying_rhythm) }}">@lang('package.price_per_month')</span>
                        </td>
                        @endforeach
                    </tr>

                    @foreach($aFeatures as $name => $packages)
                        <tr>
                            <th scope="row" class="bg-secondary">
                                {{ trans_choice('package.feature_name', $name) }}
                                <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="'{!! trans_choice('package.feature_name_tooltip', $name) !!}'"></i>
                            </th>
                            @foreach($packages as $package)
                            <td>
                                @if($package['status'] == 0)
                                    <i class="icon icon-circle-with-cross text-red" v-b-popover.hover.click="'{{ trans('package.feature_not_included') }}'"></i>
                                @else
                                    @if($package['units'] === 0)
                                        <i class="icon-check text-green" v-b-popover.hover.click="'{{ trans('package.feature_included') }}'"></i>
                                    @elseif(is_numeric($package['units']))
                                        @if($name == 'statistics')
                                            <div id="popstats-{{ $package['units'] }}" class="font-weight-light">{{ trans_choice('package.statistics_name', $package['units']) }}</div>
                                            <b-popover
                                                    :target="`popstats-{{ $package['units'] }}`"
                                                    title="Auswertungen & Features"
                                                    placement="left"
                                                    triggers="hover focus click"
                                            >
                                                {!! trans_choice('package.statistics_tooltip', $package['units']) !!}
                                            </b-popover>
                                        @elseif($package['units'] === 1)
                                            1
                                        @else
                                            @lang('package.feature_units', ['count' => $package['units']])
                                        @endif
                                    @else
                                        {{ $package['units'] }}
                                        @if($name == 'storage')
                                            <br>
                                            <span class="small font-weight-light">@lang('package.hint_storage_renewal_frequency')</span>
                                        @endif
                                    @endif
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <th scope="row"></th>
                        @foreach($aPackages as $oPackage)
                        <td>
                            <a class="btn btn-link" href="{{ route('package.order', ['id' => $oPackage->package_id, 'name' => trans_choice('package.package_name', $oPackage->package_name)]) }}">@lang('package.link_order')</a>
                        </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
                <span>Feature, Leistung oder Paket nicht gefunden? Bei Fragen zu besonderen Anforderungen, z.B. für <a href="{{ route('package.order', ['id' => 7, 'name' => trans_choice('package.package_name', 'agency')]) }}">Agenturen</a>, Vermarktungslösungen (Ad-Server, Dynamische Ad-Insertion für Podcast-Werbung, Targeting-gesteuerte Intros/Outros, etc.), HD-Videos oder Archive, <a href="{{ route('contactus.create') }}">@lang('package.link_contact_us') &rsaquo;</a></span>
            </div>
            <!--end of col-->
            <hr>
        </div>
        <!--end of row-->
        <div class="row section-outro justify-content-center mb-5">
            <div class="col-auto">
                <h4>@lang('package.header_customer_opinions')</h4>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <ul class="row feature-list justify-content-center">

            @foreach($reviews as $review)
            <li class="col-12 col-md-6 col-lg-5">
                <div class="media">
                    <img alt="{{ $review['podcast'] }}" src="{{ asset('storage/reviews/images/' . $review['logo']) }}" class="avatar avatar-square" />
                    <div class="media-body">
                        <blockquote class="mb-1">
                            {!! $review['cite'] !!}
                        </blockquote>
                        <small>@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</small>
                    </div>
                </div>
            </li>
            <!--end of col-->
            @endforeach

        </ul>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
<section>
    <div class="container">
        <div class="row justify-content-center section-intro">
            <div class="col-auto">
                <h2 class="h1">@lang('package.header_faq')</h2>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <ul class="row feature-list feature-list-sm justify-content-center">

            @foreach($faqs as $faq)
            <li class="col-12 col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('faq.show', ['id' => $faq->faq_id, 'slug' => Str::slug($faq->question)]) }}">
                            <h6 class="h5">{{ $faq->question }}</h6>
                            <span class="text-muted">
                                {!! Str::limit(strip_tags($faq->answer), $limit = 300) !!}
                            </span>
                        </a>
                    </div>
                </div>
            </li>
            @endforeach

        </ul>
        <!--end of row-->

        <div class="row justify-content-center">
            <div class="col-auto">
                <a href="{{ route('faq.index') }}" class="btn btn-outline-primary">Zu unserer FAQ: Alle Fragen und Antworten</a>
            </div>
            <!--end of col-->
        </div>

        <div class="row justify-content-center text-center section-outro">
            <div class="col-lg-4 col-md-5">
                <h6>@lang('package.header_further_questions')</h6>
                <p>@lang('package.lead_further_questions')</p>
                <a href="{{ route('contactus.create') }}">@lang('package.link_contact_packages') &rsaquo;</a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

{{--
@push('scripts')
    <script src="{{ mix('js1/package.js') }}"></script>
@endpush--}}
