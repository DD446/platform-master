@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('bills') }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row">
                <div class="col m-3">
                    <h3 id="ba">{{ trans('bills.header_bills') }}</h3>
                    <span>{{trans('bills.subheader_bills')}}</span>
                </div>
                <!--end of col-->
                <div class="col-auto">
                    <a href="{{ route('accounting.create') }}">
                        <button class="btn btn-podcaster"><i
                                class="icon-credit-card mr-1"></i> @lang('accounting.link_add_funds')</button>
                    </a>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section>
        <div class="container card p-4" id="app" data-type="billing">

            <div class="text-center" v-if="false">
                <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">...</span>
                </div>
            </div>

            <b-card v-cloak>
                <b-tabs card justified>
                    <b-tab :title="$t('bills.title_tab_bills')" {{ $canDownloadBills == 1 ? 'active' : 'disabled' }} ref="billtab">
                        <b-card-text>
                            <b-list-group flush>
                                <li class="list-group-item">
                                    <div class="row font-weight-bolder">
                                        <div class="col-12 col-sm-5">
                                            {{ trans('bills.title_bill') }}
                                        </div>
                                        <div class="col-12 col-sm-3 text-sm-center">
                                            {{ trans('bills.title_bill_date') }}
                                        </div>
                                        <div class="col-12 col-sm-3 text-sm-right">
                                            {{ trans('bills.title_bill_amount') }}
                                        </div>
                                        <div class="col-12 col-sm-1">
                                        </div>
                                    </div>
                                </li>
                                @forelse($bills as $bill)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-12 col-sm-5">
                                            @if($bill->is_refunded)
                                                <span class="badge badge-warning" title="{{trans('bills.title_refunded_bill')}}">@lang('bills.label_refunded')</span>
                                            @elseif($bill->is_paid)
                                                <span class="badge badge-success" title="{{trans('bills.title_unpaid_bill')}}">@lang('bills.label_paid')</span>
                                            @else
                                                <span class="badge badge-danger">@lang('bills.label_unpaid')</span>
                                            @endif
                                            <a href="{{ route('rechnung.show', [ 'id' => $bill->bill_id ]) }}" onclick="window.open(this.href, '_top');return false;">{{ $bill->bill_id }}</a>
                                        </div>
                                        <div class="col-12 col-sm-3 text-sm-center" title="{{$bill->date_created->formatLocalized('%d.%m.%Y %H:%M')}}">
                                            {{$bill->date_created->formatLocalized('%d. %B %Y')}}
                                        </div>
                                        <div class="col-12 col-sm-3 text-sm-right">
                                            {{ $bill->amount }} {{ $bill->currency }}
                                        </div>
                                        <div class="col-12 col-sm-1">
                                            @if(!$bill->is_refunded)
                                            <a class="btn btn-primary btn-sm" href="{{route('bills.download', ['id' => $bill->bill_id])}}" download title="{{trans('bills.title_download_bill_as_pdf')}}">
                                                @lang('bills.label_button_label_bill_as_pdf')
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @empty
                                    <li class="list-group-item">
                                        {{trans('bills.text_no_bills')}} <a href="{{route('accounting.create')}}">@lang('bills.text_add_funds')</a>
                                    </li>
                                @endforelse
                            </b-list-group>

                            <div class="mt-2">
                                {{ $bills->links() }}
                            </div>
                        </b-card-text>
                    </b-tab>

                    <b-tab :title="$t('bills.title_tab_billing_contact')" {{ $canDownloadBills == 1 ? '' : 'active' }}>
                        <b-card-text>
                            <div class="row">
                                <div class="col-11">
                                    <h3>{{ trans('bills.header_billing_address') }}</h3>

                                    <billing-address
                                        :countries="{{ json_encode($countries) }}"
                                        :user="{{ auth()->user()->userbillingcontact ?? auth()->user() }}"
                                        :can-download-bills="{{ $canDownloadBills == 1 ? 'true' : 'false' }}"></billing-address>
                                </div>
                            </div>
                        </b-card-text>
                    </b-tab>
                </b-tabs>
            </b-card>
        </div>
    </section>

@endsection('content')
