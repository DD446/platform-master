@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('fundsadd') }}
@endsection

@section('content')

    <section>
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col">
                    <div class="media align-items-center">
{{--                        <a href="#" class="mr-4">
                            <img alt="Image" src="assets/img/graphic-product-paydar-thumb.jpg" class="avatar avatar-lg avatar-square">
                        </a>--}}
                        <div class="media-body">
                            <div class="mb-3">
                                <h1 class="h2 mb-2">@lang('accounting.header_funds_add_paypal')</h1>
                                <span>@lang('accounting.description_funds_add_paypal')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section class="container">
        <div class="row">
            <div class="col-12">
                <h3>
                    {{ trans('accounting.header_payment_methods_paypal_check') }}
                    <small class="form-text text-muted">@lang('accounting.help_amount_paypal_check', ['amount' => $amount])</small>
                </h3>

                <form action="https://www.paypal.com/cgi-bin/webscr" id="payment_form" method="post">
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
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg">@lang('accounting.button_paypal_pay_sum', ['amount' => $amount])</button>
                    </div>
                    <!--		<p class="small">
                                *abzgl. 1,5% - 4,8% vom Einzahlungsbetrag zzgl. 0,35 € pro Transaktion (<a href="https://www.paypal.com/" target="_blank">abhängig vom Land</a>)
                            </p>-->
                </form>
            </div>
        </div>
    </section>

@endsection('content')