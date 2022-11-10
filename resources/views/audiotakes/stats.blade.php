@extends('main')

@section('content')
    <section class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-6">
                <img src="{{ asset('/images1/podcast-pioniere-250px.png') }}" alt="Podcast Pioniere Logo" class="img img-fluid left mr-3" style="max-height: 85px">
            </div>
            <div class="col-12 col-lg-6">
                <img src="{{ asset('/images1/audiotakes.svg') }}" alt="audiotakes Logo" class="img img-fluid right ml-3" style="max-height: 85px">
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <h1 class="display-4">
                    {{ trans('audiotakes.header_stats') }}
                </h1>

                <div class="col-auto text-center">
                    <span class="lead">{{trans('audiotakes.header_lead_feed', ['feed' => $feedTitle])}}</span>
                </div>
            </div>
        </div>

        <div class="col mt-5">
            <div class="card height-20 bg-primary text-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <span class="h5">@lang('audiotakes.header_estimated_revenue')</span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6 col-md-2 mt-2 text-center">
                            <span class="h1 mb-0">{{$aRevenue['today']}}&euro;</span>
                            <span>{{ trans('audiotakes.revenue_today') }}</span>
                        </div>
                        <div class="col-6 col-md-2 mt-2 text-center">
                            <span class="h1 mb-0">{{$aRevenue['yesterday']}}&euro;</span>
                            <span>{{ trans('audiotakes.revenue_yesterday') }}</span>
                        </div>
                        <div class="col-6 col-md-2 mt-2 text-center">
                            <span class="h1 mb-0">{{$aRevenue['last7days']}}&euro;</span>
                            <span>{{ trans('audiotakes.revenue_last_7_days') }}</span>
                        </div>
                        <div class="col-6 col-md-3 mt-2 text-center">
                            <span class="h1 mb-0">{{$aRevenue['thisMonth']}}&euro;</span>
                            <span>{{ trans('audiotakes.revenue_this_month') }}</span>
                        </div>
                        <div class="col-6 col-md-3 mt-2 text-center">
                            <span class="h1 mb-0">{{$aRevenue['last30days']}}&euro;</span>
                            <span>{{ trans('audiotakes.revenue_last_30_days') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="space-sm">
            <div class="container">
                <div class="row justify-content-around">
                    <div class="col-auto text-center mt-2">
                        <span class="h1 mb-0">{{$results->total->inventory}}</span>
                        <span>{{ trans('audiotakes.adswizz_metrics.inventory') }}</span>
                    </div>
                    <!--end of col-->
                    <div class="col-auto text-center mt-2">
                        <span class="h1 mb-0">{{$results->total->objectiveCountableSum}}</span>
                        <span>{{ trans('audiotakes.adswizz_metrics.objectiveCountableSum') }}</span>
                    </div>
                    <!--end of col-->
                    <div class="col-auto text-center mt-2">
                        <span class="h1 mb-0">{{$results->total->listenerIdHLL}}</span>
                        <span>{{ trans('audiotakes.adswizz_metrics.listenerIdHLL') }}</span>
                    </div>
                    <!--end of col-->
                    <div class="col-auto text-center mt-2">
                        <span class="h1 mb-0">{{round($results->total->fillRate, 2)}}%</span>
                        <span>{{ trans('audiotakes.adswizz_metrics.fillRate') }}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
                <div class="row justify-content-around mt-2">
                    <div class="col-auto text-center">
                        <span class="help-block">Zahlen für den Zeitraum vom {{$from}} bis {{$to}}</span>
                    </div>
                </div>
            </div>
            <!--end of container-->
        </section>

        <section>
            <div class="container-fluid bg-white p-3">
                <div class="row mt-4">
                    <div class="col-12 col-lg-5 text-center">
                        <h3>Abrufe</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Datum</th>
                                <th scope="col">{{ trans('audiotakes.adswizz_metrics.inventory') }}</th>
                                <th scope="col">{{ trans('audiotakes.adswizz_metrics.objectiveCountableSum') }}</th>
                                <th scope="col">{{ trans('audiotakes.adswizz_metrics.listenerIdHLL') }}</th>
                                <th scope="col">{{ trans('audiotakes.adswizz_metrics.fillRate') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($results->data as $key => $entry)
                                <tr>
                                    <td>
                                        {{\Carbon\CarbonImmutable::createFromTimestamp(strtotime($entry->total->__time->start))->toFormattedDateString()}}
                                    </td>
                                    <td>
                                        {{$entry->total->inventory}}
                                    </td>
                                    <td>
                                        {{$entry->total->objectiveCountableSum}}
                                    </td>
                                    <td>
                                        {{$entry->total->listenerIdHLL}}
                                    </td>
                                    <td>
                                        {{round($entry->total->fillRate,2)}}%
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 col-lg-5">
                        <h3>Regionalität</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Land</th>
                                <th scope="col">Region</th>
                                <th scope="col">Stadt</th>
                                <th scope="col">Inventar</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($regions->data as $country)
                                @isset($country->total->geoCountryName)
                                @if($country->total->inventory > 0)
                                    <tr>
                                        <td colspan="3">
                                            {{Illuminate\Support\Str::before($country->total->geoCountryName,'(')}}
                                        </td>
                                        <td>
                                            {{ $country->total->inventory }}
                                        </td>
                                    </tr>
                                    @foreach($country->data as $region)
                                        @isset($region->total->geoRegionName)
                                        @if($region->total->inventory > 0)
                                            <tr>
                                                <td></td>
                                                <td colspan="2">
                                                    {{Illuminate\Support\Str::before($region->total->geoRegionName,'(')}}
                                                </td>
                                                <td>
                                                    {{ $region->total->inventory }}
                                                </td>
                                            </tr>
                                            @foreach($region->data as $city)
                                                @isset($city->total->geoCity)
                                                    @if($city->total->inventory > 0)
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                {{ $city->total->geoCity }}
                                                            </td>
                                                            <td>
                                                                {{ $city->total->inventory }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endisset
                                            @endforeach
                                            @endif
                                        @endisset
                                    @endforeach
                                    @endif
                                @endisset
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 col-lg-2 text-center">
                        <h3>Demographie</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Altersgruppe</th>
                                    <th scope="col">{{ trans('audiotakes.label_distribution') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($ages as $key => $entry)
                                <tr>
                                    <td>
                                        {{$key}}
                                    </td>
                                    <td>
                                        {{$entry['percent']}}%
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <section>
        <div class="row">
            <div class="col-11 text-right">
                v0.1
            </div>
        </div>
    </section>
@endsection('content')
