<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card card-lg w-100">
                <div class="row no-gutters justify-content-between card-header">
                    <div class="col-auto">
                        <span>@lang('audiotakes.billing_company_name')</span>
                        <address>
                            @lang('audiotakes.billing_company_address_street')
                            <br>@lang('audiotakes.billing_company_address_city')
                            <br>@lang('audiotakes.billing_company_address_country')
{{--                            <br>
                            <a href="mailto:{{trans('audiotakes.billing_company_address_email')}}">@lang('audiotakes.billing_company_address_email')</a>--}}
                        </address>
                    </div>
                    <div class="col-auto col-lg-4">
                        <img alt="Logo" src="{{ asset('images1/audiotakes.svg') }}" class="img-fluid">
                    </div>
                </div>
                <div class="card-body">
                    <h1 class="h2">
                        @lang('audiotakes.header_creditvoucher')
                    </h1>
                    <div class="row no-gutters justify-content-between">
                        <div class="col-12 col-sm-auto">
                            <span>
                                {{$payment->audiotakes_contract_partner->first_name}} {{$payment->audiotakes_contract_partner->last_name}}
                                @if($payment->audiotakes_contract_partner->organisation)
                                    <br>{{$payment->audiotakes_contract_partner->organisation}}
                                @endif
                            </span>
                            <address>
                                {{$payment->audiotakes_contract_partner->street}} {{$payment->audiotakes_contract_partner->housenumber}}
                                <br>{{$payment->audiotakes_contract_partner->post_code}} {{$payment->audiotakes_contract_partner->city}}
                                <br>{{$payment->country_spelled}}
                                @if($payment->is_reverse_charge)
                                    <br>
                                    @lang('audiotakes.label_vatid_short'): {{$payment->audiotakes_payout_contact->vat_id}}
                                @endif
                            </address>
                        </div>
                        <!--end of col-->
                        <div class="col-12 col-md-8">
                            <ul class="list-unstyled">
                                <li class="row">
                                    <span class="font-weight-bold col-7 text-md-right">
                                        @lang('audiotakes.label_creditvoucher_number')
                                    </span>
                                    <span class="col">{{ $payment->billing_number }}</span>
                                </li>
                                <li class="row">
                                    <span class="font-weight-bold col-7 text-md-right">
                                        @lang('audiotakes.label_creditvoucher_date')
                                    </span>
                                    <span class="col">{{$payment->created_at->format('d.m.Y')}}</span> {{--TODO: I18N--}}
                                </li>
                                <li class="row">
                                    <span class="font-weight-bold col-7 text-md-right">
                                        @lang('audiotakes.label_creditvoucher_customernumber')
                                    </span>
                                    <span class="col">
                                        {{ $payment->customer_number }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <div class="card-body pt-0">
                    <table class="table text-right">
                        <thead class="bg-secondary">
                        <tr>
                            <th scope="col" class="text-left">@lang('audiotakes.label_position')</th>
                            <th scope="col" class="text-left">@lang('audiotakes.label_item')</th>
                            <th scope="col" class="text-right">@lang('audiotakes.label_amount_sum')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-left">1</td>
                            <td class="text-left">
                                @lang('audiotakes.text_billing_item')
                            </td>
                            <td class="text-nowrap">
                                {{$payment->funds}} {{$payment->currency}}
                            </td>
                        </tr>
                        <tr class="border-b-8">
                            <td></td>
                            <td>
                                @lang('audiotakes.label_amount_net')
                            </td>
                            <td class="text-nowrap">
                                {{$payment->funds}} {{$payment->currency}}
                            </td>
                        </tr>
                        @if($payment->amount_tax)
                        <tr>
                            <td></td>
                            <td>
                                @lang('bills.label_tax_with_vat', ['vat' => $payment->amount_vat])
                            </td>
                            <td>
                                {{$payment->amount_tax}} {{$payment->currency}}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td>
                            </td>
                            <td>
                                @lang('audiotakes.label_amount_gross')
                            </td>
                            <td>
                                <span class="font-weight-bolder text-nowrap">{{$payment->amount_gross}} {{$payment->currency}}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    @if($payment->is_reverse_charge)
                        <div class="row justify-content-between mt-3">
                            <div class="col-auto">
                                @lang('bills.text_bill_hint_reverse_charge_header')
                                <br>
                                <small>
                                    @lang('bills.text_bill_hint_reverse_charge')
                                </small>
                            </div>
                        </div>
                    @endif

                    <div class="row justify-content-between">
                        <div class="col-auto">
                            @if($payment->isPrivate())
                                <p>
                                    <small>
                                        @lang('audiotakes.label_creditvoucher_signature_tax_hint_private')
                                    </small>
                                </p>
                            @endif
                            <p>
                                <small>
                                    @lang('audiotakes.label_creditvoucher_signature_disclaimer')
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <!--end of card body-->
                <div class="card-footer">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('audiotakes.billing_company_name')
                                </li>
                                <li>
                                    @lang('audiotakes.billing_company_address_street')
                                </li>
                                <li>
                                    @lang('audiotakes.billing_company_address_city')
                                </li>
                                <li class="">
                                    @lang('audiotakes.billing_company_phone')
                                </li>
                                <li class="">
                                    <a href="mailto:{{ trans('audiotakes.billing_company_address_email') }}">
                                        @lang('audiotakes.billing_company_address_email')
                                    </a>
                                </li>
                                <li class="">
                                    <a href="{{ trans('audiotakes.billing_company_address_web') }}">
                                        @lang('audiotakes.billing_company_address_web')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('audiotakes.billing_company_register_number')
                                </li>
                                <li class="">
                                    @lang('audiotakes.billing_company_register_court')
                                </li>
                                <li>
                                    @lang('audiotakes.label_vatid_short'): @lang('audiotakes.vatid')
                                </li>
                                <li class="">
                                    @lang('audiotakes.billing_company_tax_bureau')
                                </li>
                                <li class="">
                                    @lang('audiotakes.label_ceo'):<br>@lang('audiotakes.billing_company_ceo')
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('audiotakes.billing_company_bank_name')
                                </li>
                                <li class="">
                                    IBAN: @lang('audiotakes.billing_company_bank_iban')
                                </li>
                                <li class="">
                                    BIC/Swift: @lang('audiotakes.billing_company_bank_bic')
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
