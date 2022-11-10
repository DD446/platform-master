@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('fundsadd') }}
@endsection

@section('content')

    <div id="app" data-type="funds">

        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col">
                        <div class="media align-items-center">
                            <div class="media-body">
                                <div class="mb-3">
                                    <h1 class="h2 mb-2">@lang('accounting.header_funds_add') <funds :amount="{{ auth()->user()->funds }}"></funds></h1>
                                    <span>@lang('accounting.description_funds_add')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end of col-->
                    <div class="col-auto">
                        <a href="{{ route('rechnung.index') }}" class="btn btn-secondary text-white">@lang('accounting.link_bills')</a>
                    </div>
                    <div class="col-auto">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                            <!--                            <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                                                           onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                                            <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                                                        </a>-->
                            <a href="https://www.podcaster.de/faq/antwort-36-wie-funktioniert-die-bezahlung"
                               class="d-none d-sm-block"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;">
                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                            </a>
                        </div>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        <section>
            <div class="container card p-4">
                <b-card no-body>
                    <b-tabs card fill>
                        <b-tab :title="$t('accounting.title_tab_paypal')" active>
                            <b-card-text>
                                <h3>
                                    {{ trans('accounting.header_payment_methods_paypal') }}
                                    <small class="form-text text-muted">@lang('accounting.help_amount_paypal')</small>
                                </h3>

                                <form action="https://www.paypal.com/cgi-bin/webscr" id="payment_form" method="post" target="_blank">

                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-sm-12">
                                            <div class="form-group mr-2">
                                                <input name="cmd" value="_xclick" type="hidden">
                                                <input name="business" value="guthaben@podcaster.de" type="hidden">
                                                <input name="item_name" value="@lang('accounting.text_paypal_item_name')" type="hidden">
                                                <input name="item_number" value="4" type="hidden">
                                                <input name="no_shipping" value="1" type="hidden">
                                                <input name="no_note" value="0" type="hidden">
                                                <input name="currency_code" value="EUR" type="hidden">
                                                <input name="country" value="{{ auth()->user()->country ?? 'DE' }}" type="hidden" id="country">
                                                <input name="amount" value="25" type="hidden">
                                                <input name="first_name" value="{{ auth()->user()->first_name }}" type="hidden">
                                                <input name="last_name" value="{{ auth()->user()->last_name }}" type="hidden">
                                                <input name="email" value="{{ auth()->user()->email }}" type="hidden">
                                                <input name="cbt" value="{{ config('app.name') }}" type="hidden">
                                                <input name="custom" value="{{ auth()->id() }}" type="hidden">
                                                <input name="return" value="{{ route('accounting.create') }}?success=paypal" type="hidden">
                                                <input name="cancel_return" value="{{ route('accounting.index') }}" type="hidden">
                                                <input name="notify_url" value="https://www.podcaster.de/zahlung/ipn/annehmen/" type="hidden">

                                                <select name="amount" class="form-control" id="paypalAmount">
                                                    @foreach($aAmount as $sum => $label)
                                                        <option value="{{ $sum }}" @if(old('amount', 25) == $sum) selected @endif>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7 col-sm-12">
                                            <button type="submit" class="btn btn-primary">@lang('accounting.button_paypal_pay_sum')</button>
                                        </div>
                                    </div>
                                </form>
                            </b-card-text>
                        </b-tab>

                        <b-tab :title="$t('accounting.title_tab_bank')">
                            <b-card-text>
                                <h3>
                                    {{ trans('accounting.header_payment_methods_bank_transfer') }}
                                    <small class="form-text text-muted">@lang('accounting.help_amount_bank_transfer')</small>
                                </h3>

                                <div class="row">
                                    <div class="col-lg-6 col-md-5 col-sm-12">
                                        <dl class="wide padtop10">
                                            <dt>Zahlungsempfänger</dt><dd>Fabio Bacigalupo</dd>
                                            <dt>IBAN</dt><dd>DE60100800000579632500</dd>
                                            <dt>SWIFT/BIC</dt><dd>DRESDEFF100</dd>
                                            <dt>Bank</dt><dd>Commerzbank</dd>
                                            <dt>Verwendungszweck</dt><dd>POD-{{ auth()->id() }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-lg-6 col-md-7 col-sm-12">
                                        <h5>aus Deutschland</h5>
                                        <p class="std">
                                            Bearbeitungszeit i.d.R. 2-4 Werktage
                                        </p>
                                        <div class="padtop10"></div>
                                        <h5>aus Europa</h5>
                                        <p class="std">
                                            Bearbeitungszeit i.d.R. 3-5 Werktage
                                        </p>
                                        <div class="padtop10"></div>
                                        <h5>ausserhalb Europas</h5>
                                        <p class="std">
                                            Bearbeitungszeit i.d.R. 6-10 Werktage
                                        </p>
                                    </div>
                                </div>
                            </b-card-text>
                        </b-tab>

                        <b-tab :title="$t('accounting.title_tab_bill')" lazy>
                            <b-card-text>
                                <h3>
                                    {{ trans('accounting.header_payment_methods_bill') }}
                                    <small class="form-text text-muted">@lang('accounting.help_amount_bill')</small>
                                </h3>

                                @if(!auth()->user()->can_pay_by_bill)
                                    <div class="row">
                                        <div class="col-6">
                                            <p>@lang('accounting.help_who_is_bill_payment_for')</p>
                                        </div>
                                        <div class="col-6">
                                            <a class="btn btn-podcaster btn-lg" href="{{ route('contactus.create')  }}?enquiry_type=bill&comment=Bitte%20schaltet%20uns%20f%C3%BCr%20die%20Zahlung%20auf%20Rechnung%20frei.%20Wir%20buchen%20daf%C3%BCr%20einen%20Betrag%20i.H.v.%20???%20EUR.">@lang('accounting.button_bill_pay_contact')</a>
                                        </div>
                                    </div>
                                @else
                                    <bill></bill>
                                @endif
                            </b-card-text>
                        </b-tab>

                        <b-tab :title="$t('accounting.title_tab_voucher')" lazy>
                            <b-card-text>
                                <h3>
                                    {{ trans('accounting.header_payment_methods_voucher') }}
                                    <small class="form-text text-muted">@lang('accounting.help_amount_voucher')</small>
                                </h3>

                                <voucher></voucher>
                            </b-card-text>
                        </b-tab>

                        <b-tab :title="$t('accounting.title_tab_audiotakes')" lazy>
                            <b-card-text>
                                <h3>
                                    {{ trans('accounting.header_payment_methods_audiotakes') }}
                                    <small class="form-text text-muted">@lang('accounting.help_amount_audiotakes')</small>
                                </h3>

                                Du findest das Formular zum Transfer Deines Guthabens aktuell noch
                                <a href="/vermarktung">auf der Vermarktungsseite</a>.
                            </b-card-text>
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>
        </section>

    </div>

@endsection('content')
