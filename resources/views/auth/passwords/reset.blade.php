@extends('main')

@section('content')
<section class="space-sm">
    <div class="container align-self-start">
        <div class="row mb-5 d-none d-md-block">
            <div class="col text-center">
                <a href="{{ route('home') }}"><img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}" /></a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7">
                <div class="card card-lg text-center">
                    <div class="card-body">
                        <div class="mb-5">
                            <h1 class="h2 mb-2">{{trans('login.header_reset_password')}}</h1>
                            <span>{{trans('login.subheader_reset_password')}}</span>
                        </div>
                        <div class="row no-gutters justify-content-center">
                            <form class="text-left col-lg-9" method="post" action="{{ route('password.update') }}">
                                {{ csrf_field() }}

                                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                                <div class="form-group{{ $errors->has('email') ? ' alert alert-danger' : '' }}">
                                    <label for="email">@lang('login.label_email')</label>
                                    <input id="email" type="email" class="form-control form-control-lg" name="email" value="{{ $email ?? old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' alert alert-danger' : '' }}">
                                    <label for="password">@lang('login.label_password')</label>
                                    <input id="password" type="password" class="form-control form-control-lg" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password_confirmation') ? ' alert alert-danger' : '' }}">
                                    <label for="password-confirm">@lang('login.label_confirm_password')</label>
                                        <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required>

                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                </div>

                                <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            @lang('login.button_reset_password')
                                        </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
