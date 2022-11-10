<section class="bg-info text-light">
    <div class="container-fluid text-center">
        <div class="row justify-content-center">
            <div class="col">
                <span class="title-decorative">@lang('package.subtitle_user')</span>
                <h2 class="display-4">@lang('package.header_user')</h2>
                <span class="lead">@lang('package.lead_user')</span>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->

<section id="app" data-type="packages">
    <div class="container-fluid">
        <div class="row justify-content-center text-center section-intro">
            <div class="col-12 col-md-9 col-lg-8">
                <alert-container></alert-container>

                @if(auth()->user()->new_package_id)
                    @php
                        $newPackage = \App\Models\Package::find(auth()->user()->new_package_id);
                        $accountingTimes = get_user_accounting_times(auth()->user()->id);
                    @endphp
                    <div class="alert alert-info" role="alert">
                        {!! trans('package.message_success_package_downgrade_saved', ['name' =>
                        trans_choice('package.package_name', $newPackage->package_name), 'date' =>
                        $accountingTimes['nextTimeFormatted']]) !!}
                    </div>
                @endif
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="row justify-content-center">
            <div class="col">
                <table class="table table-bordered table-responsive pricing">
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
                        <th scope="row" class="bg-secondary">@lang('package.header_price')</th>
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
                                        <i class="icon-circle-with-cross text-red" v-b-popover.hover.click="'{{ trans('package.feature_not_included') }}'"></i>
                                    @else
                                        @if($package['units'] === 0)
                                            <i class="icon-check text-green" v-b-popover.hover.click="'{{ trans('package.feature_included') }}'"></i>
                                        @elseif(is_numeric($package['units']))
                                            @if($name == 'statistics')
                                                <div id="popstats-{{ $package['units'] }}" class="font-weight-light">{{ trans_choice('package.statistics_name', $package['units']) }}</div>
                                                <b-popover
                                                        :target="`popstats-{{ $package['units'] }}`"
                                                        title="Auswertungen"
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
                                <div class="spinner-grow m-3" role="status" v-if="false">
                                    <span class="sr-only">@lang('package.text_loading')</span>
                                </div>
                                <package-switch
                                        package-id="{{ $oPackage->package_id }}"
                                        package-id-selected="{{ auth()->user()->package_id }}"
                                        package-name="{{ trans_choice('package.package_name', $oPackage->package_name) }}"
                                        package-change-route="{{ route('package.change') }}"></package-switch>
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--end of col-->
            <hr>
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
