@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('faq') }}
@endsection

@section('content')
    <section class="bg-dark text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/faq_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
        {{--{{ $page->getFirstMedia('bg') }}--}}

        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <span class="title-decorative">{{trans('payment.header')}}</span>
                    <h1 class="display-4">{{trans('payment.subheader')}}</h1>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <!--end of section-->
    <section class="flush-with-above">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto">

                    <div class="module_half left">
                        <p>Mein Guthaben: <span class="strong">{{ auth()->user()->funds }} &euro;</span></p>
                    </div>

                    <div class="module_full">
                        <h3>{{ trans('payment.header_paypal') }}</h3>
                        <div class="padtop10">
                            <form action="#" method="post" class="std">
                                <fieldset>
                                    <select name="frmAmount">
                                    </select>
                                    <input class="btn btn-primary right" name="submitted" value="Bezahlen" type="submit">
                                    <p class="std">
                                        Betrag wird sofort verbucht
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->

            <div class="row">
                <div class="col-6">

                    <h3>{{ trans('payment.header_bank') }}</h3>
                        <dl class="">
                            <dt>Zahlungsempfänger</dt><dd>Fabio Bacigalupo</dd>
                            <dt>IBAN</dt><dd>DE60100800000579632500</dd>
                            <dt>SWIFT/BIC</dt><dd>DRESDEFF100</dd>
                            <dt>Bank</dt><dd>Commerzbank</dd>
                            <dt>Verwendungszweck</dt><dd>POD-{loggedOnUserID}</dd>
                        </dl>
                </div>

                <div class="col-6">

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

            <div class="row">
                <div class="col-auto">

                    <h3>{{ trans('payment.header_bill') }} Zahlung auf Rechnung</h3>

                    <div class="">
                        Für Vereine, Stiftungen, Universitäten und andere Organisationen, die Zahlungsanweisungen nur nach Rechnungsstellung durchführen können, bieten wir Zahlung auf Rechnung an.
                    </div>

                    <div class="">
                        <a href="/kontakt?enquiry_type=bill&comment=Bitte schaltet uns für die Zahlung auf Rechnung frei. Wir buchen dafür einen Betrag i.H.v. ??? EUR.">
                            <button class="btn btn-secondary" name="submitted" value="Bezahlen" type="button">Zahlung auf Rechnung beantragen</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-auto">

                    <h3>{{ trans('payment.header_voucher') }}</h3>

                    <div class="">
                        <form>
                            <input type="text" class="form-group">
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
