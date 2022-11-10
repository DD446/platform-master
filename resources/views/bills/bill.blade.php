<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card card-lg w-100">
                <div class="row no-gutters justify-content-between card-header">
                    <div class="col-auto">
                        <span>@lang('bills.billing_company_name')</span>
                        <br><span>@lang('bills.billing_company_brand')</span>
                        <address>
                            @lang('bills.billing_company_address_street')
                            <br>@lang('bills.billing_company_address_city')
                            <br>@lang('bills.billing_company_address_country')
                            <br>
                            {{--<a href="mailto:{{trans('bills.billing_company_address_email')}}">@lang('bills.billing_company_address_email')</a>--}}
                        </address>
                        <span class="text-muted">@lang('bills.label_vatid_short'): @lang('bills.text_fabio_vatid')</span>
                    </div>
                    <!--end of col-->
                    <div class="col-auto col-lg-4">
                        <img alt="Logo" src="{{ asset('images1/podcaster.de-Logo_1175x480_300dpi_trans.png') }}" class="img-fluid">
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
                <div class="card-body">
                    <h1 class="h2">@lang('bills.header_bill')
                        @if($payment->is_refunded)
                            <span class="badge badge-warning">@lang('bills.label_refunded')</span>
                        @elseif($payment->is_paid)
                            <span class="badge badge-success">@lang('bills.label_paid')</span>
                        @else
                            <span class="badge badge-danger">@lang('bills.label_unpaid')</span>
                        @endif
                    </h1>
                    <div class="row no-gutters justify-content-between">
                        <div class="col-12 col-sm-auto">
                            <span class="title-decorative mb-2">@lang('bills.label_bill_to'):</span>
                            <span>
                                @if($payment->payer->userbillingcontact->organisation)
                                    {{$payment->payer->userbillingcontact->organisation}}
                                @endif
                                @if($payment->payer->userbillingcontact->department)
                                    <br>{{$payment->payer->userbillingcontact->department}}
                                @endif
                                @if($payment->payer->userbillingcontact->department || $payment->payer->userbillingcontact->department)
                                    <br>
                                @endif
                                {{$payment->payer->userbillingcontact->first_name}} {{$payment->payer->userbillingcontact->last_name}}
                            </span>
                            <address>
                                {{$payment->payer->userbillingcontact->street}} {{$payment->payer->userbillingcontact->housenumber}}
                                <br>{{$payment->payer->userbillingcontact->post_code}} {{$payment->payer->userbillingcontact->city}}
                                <br>{{$payment->country_spelled}}
                                @if($payment->is_reverse_charge)
                                    <br>
                                    @lang('bills.label_vatid_short'): {{$payment->payer->userbillingcontact->vat_id}}
                                @endif
                            </address>
                        </div>
                        <!--end of col-->
                        <div class="col-12 col-md-8">
                            <ul class="list-unstyled">
                                <li class="row">
                                    <span class="font-weight-bold col-7 text-md-right">@lang('bills.label_bill_number')</span>
                                    <span class="col">{{$payment->bill_id}}</span>
                                </li>
                                <li class="row">
                                    <span class="font-weight-bold col-7 text-md-right">@lang('bills.label_bill_date')</span>
                                    <span class="col">{{$payment->date_created->format('d.m.Y')}}</span> {{--TODO: I18N--}}
                                </li>
                                @if(!$payment->is_paid)
                                    <li class="row">
                                        <span class="font-weight-bold col-7 text-md-right">@lang('bills.label_bill_due_date')</span>
                                        <span class="col">@lang('bills.label_bill_due_terms')</span>
                                    </li>
                                @endif
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
                            <th scope="col">@lang('bills.label_item')</th>
                            {{--                                    <th scope="col" class="text-right">Qty</th>
                                                                <th scope="col" class="text-right">Rate</th>--}}
                            <th scope="col" class="text-right">@lang('bills.label_amount_sum')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">
                                @lang('bills.text_billing_item', ['service' => config('app.name')])
                                <br>@lang('bills.text_billing_hint_tax')
                            </th>
                            <td>{{$payment->amount_gross}} {{$payment->currency}}</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                @lang('bills.label_amount_net')
                            </th>
                            <td>
                                {{$payment->amount_net}} {{$payment->currency}}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                @lang('bills.label_tax_with_vat', ['vat' => $payment->amount_vat])
                            </th>
                            <td>
                                {{$payment->amount_tax}} {{$payment->currency}}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                @lang('bills.label_amount_gross')
                            </th>
                            <td>
                                <span class="h3">{{$payment->amount_gross}} {{$payment->currency}}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <small>
                                @lang('bills.label_bill_signature_disclaimer')
                            </small>
                        </div>
                        @if(!$payment->is_paid)
                            <div class="col-auto">
                                <small>
                                    @lang('bills.text_bill_hint_payable')
                                </small>
                            </div>
                        @endif
                    </div>

                    @if($payment->payer->userbillingcontact->extras)
                        <div class="row justify-content-between mt-3">
                            <div class="col-auto">
                                <small>
                                    {{$payment->payer->userbillingcontact->extras}}
                                </small>
                            </div>
                        </div>
                    @endif

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
                </div>
                <!--end of card body-->
                <div class="card-footer">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('bills.billing_company_name')
                                </li>
                                <li>
                                    @lang('bills.billing_company_brand')
                                </li>
                                <li>
                                    @lang('bills.billing_company_address_street')
                                </li>
                                <li>
                                    @lang('bills.billing_company_address_city')
                                </li>
                                <li>
                                    @lang('bills.label_vatid_short'): @lang('bills.text_fabio_vatid')
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('bills.billing_company_phone')
                                </li>
                                <li class="">
                                    @lang('bills.billing_company_address_email')
                                </li>
                                <li class="">
                                    @lang('bills.billing_company_address_web')
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                        <div class="col-auto">
                            <ul class="list-unstyled">
                                <li class="">
                                    @lang('bills.billing_company_bank_name')
                                </li>
                                <li class="">
                                    IBAN: @lang('bills.billing_company_bank_iban')
                                </li>
                                <li class="">
                                    BIC/Swift: @lang('bills.billing_company_bank_bic')
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                    </div>
                </div>
            </div>
        </div>
        <!--end of col-->
    </div>
    <!--end of row-->
</div>
