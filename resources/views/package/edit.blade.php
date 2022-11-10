@extends('main')

@section('content')
    <section class="space-sm">
        <div class="container d-none d-lg-block d-xl-block">
            <div class="row">
                <div class="col text-center">
                    <a href="{{ route('home') }}">
                        <img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}" />
                    </a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <section class="height-80 flush-with-above">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-5 mb-4">
                    <img alt="Image" src="{{ asset('images1/bestellung_passwort_1.jpg') }}" class="w-100 rounded" />
                </div>
                <!--end of col-->
                <div class="col-12 col-md-7 col-lg-6 mb-4 text-center text-md-left">
                    <form action="{{ route('package.update', ['username' => $username, 'hash' => $hash]) }}" method="post" id="pwset" autocomplete="off">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user" value="{{ $username }}">

                        <h1 class="display-3">@lang('login.header_password_hint')</h1>
                        <h2 class="lead">@lang('login.subheader_password_hint')</h2>

                        <div class="form-group{{ $errors->has('password') ? ' alert alert-danger' : '' }}">
                            <input
                                autocomplete="new-password"
                                type="password"
                                class="form-control form-control-lg"
                                name="password"
                                placeholder="@lang('login.placeholder_set_password')"
                                tabindex="1" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <h1 class="display-3 mt-3">@lang('login.header_country_hint')</h1>
                        <h2 class="lead">@lang('login.subheader_country_hint')</h2>

                        <div class="form-row form-group{{ $errors->has('country') ? ' alert alert-danger' : '' }}">
                            <select name="country" id="country" required class="form-control form-control-lg" tabindex="2">
                                <option value="" @if(!old('country')) selected @endif>@lang('login.choose_country')</option>
                                <option value="" disabled></option>
                                @foreach($countriesFirst as $code => $country)
                                    <option value="{{ $code }}" @if(old('country') == $code) selected @endif>{{ $country }}</option>
                                @endforeach
                                <option value="" disabled></option>
                                @foreach($countries as $code => $country)
                                    <option value="{{ $code }}" @if(old('country') == $code) selected @endif>{{ $country }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('country'))
                                <span class="help-block pt-2">
                                    <strong>{{ $errors->first('country') }}</strong>
                                </span>
                            @endif
                        </div>

                        @if($askCreditCard)
                        <div class="row mb-2 mt-4">
                            <div class="col-12">
                                <h4>@lang('login.header_credit_card')</h4>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username">@lang('login.label_creditcard_full_name')</label>
                            <input type="text" name="owner_name" placeholder="@lang('login.placeholder_creditcard_full_name')" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="cardNumber">@lang('login.label_creditcard_card_number')</label>
                            <div class="input-group">
                                <input type="text" name="card_number" minlength="13" maxlength="19" placeholder="@lang('login.placeholder_creditcard_card_number')" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text text-muted">
                                        <i class="fa fa-cc-visa mx-1"></i>
                                        <i class="fa fa-cc-amex mx-1"></i>
                                        <i class="fa fa-cc-mastercard mx-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label><span class="hidden-xs">@lang('login.label_creditcard_expiration_date')</span></label>
                                    <div class="input-group">
                                        <input type="number" min="1" max="12" minlength="2" maxlength="2" placeholder="@lang('login.placeholder_creditcard_expiration_date_month')" name="expiration_month" class="form-control" required>
                                        <input type="number" minlength="2" maxlength="4" placeholder="@lang('login.placeholder_creditcard_expiration_date_year')" name="expiration_year" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-4">
                                    <label data-toggle="tooltip" title="@lang('login.title_cvv')">CVV
                                        <i class="fa fa-question-circle"></i>
                                    </label>
                                    <input type="number" min="100" max="999" maxlength="3" minlength="3" name="cvc" required class="form-control" placeholder="@lang('login.placeholder_creditcard_cvv')">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($showCaptcha)
                        <div class="form-group {{ $errors->has('mathcaptcha') ? ' alert alert-danger' : '' }} clearfix">

{{--                            <div class="form-group  clearfix">
                                <label for="mathgroup">8 + 4 (Löse die Rechenaufgabe!)</label>
                                <input class="form-control" id="mathcaptcha" placeholder="Gib hier das Ergebnis der Rechenaufgabe ein." type="text" name="mathcaptcha" required="required" value="">
                            </div>--}}

{{--                            <label for="mathgroup">{{ trans('contact_us.label_math_equation', ['equation' => app('mathcaptcha')->label()]) }}</label>
                            {!! app('mathcaptcha')->input(['class' => 'form-control', 'id' => 'mathlabel', 'placeholder' => trans('contact_us.placeholder_mathcaptcha')]) !!}
                            @if ($errors->has('mathcaptcha'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mathcaptcha') }}</strong>
                                </span>
                            @endif
                            <br>--}}
                            @if ($errors->has('g-recaptcha-response'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                            {!! Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJs('de') !!}
                            {!! Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
                            {{--{!! Anhskohbo\NoCaptcha\Facades\NoCaptcha::displaySubmit('pwset', 'Passwort speichern', ['class' => 'btn btn-lg btn-primary']) !!}--}}
                        </div>
                        @endif
                        <div class="form-group">
                            <button class="btn btn-lg btn-primary float-right">@lang('login.button_save_data')</button>
                        </div>
                    </form>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
