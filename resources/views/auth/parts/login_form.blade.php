<section class="space-sm">
    <div class="container align-self-start">
        <div class="row mb-5 d-md-block">
            <div class="col text-center">
                <a href="{{ route('home') }}">
                    <img alt="Podcaster Service" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}" />
                </a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7">

                <div class="card card-lg text-center">
                    <div class="card-body">
                        <div class="mb-5">
                            <h1 class="h2 mb-2">{{trans('login.header_login')}}</h1>
                            <span>{{trans('login.subheader_login')}}</span>
                        </div>
                        <div class="row no-gutters justify-content-center">
                            <form class="text-left col-lg-9" method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group{{ $errors->has('email') ? ' alert alert-danger' : '' }}">
                                    <label for="email">{{trans('login.label_email')}}</label>

                                    <input id="email" type="email" class="form-control form-control-lg" name="email"
                                           value="{{ old('email') }}" required autofocus
                                           placeholder="{{trans('login.placeholder_email')}}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' alert alert-danger' : '' }}">
                                    <label for="password">{{trans('login.label_password')}}</label>
                                    <input id="password" type="password" class="form-control form-control-lg"
                                           name="password" required placeholder="{{trans('login.placeholder_password')}}">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                    <small>{{trans('login.forgot_password')}} <a href="{{ route('password.request') }}">{{trans('login.link_reset_here')}}</a></small>
                                </div>
                                <div>
                                    <div class="custom-control custom-checkbox align-items-center">
                                        <input type="checkbox" class="custom-control-input"
                                               name="remember" {{ old('remember') ? 'checked' : '' }} id="check-remember">
                                        <label class="custom-control-label text-small"
                                               for="check-remember">{{trans('login.label_remember_me')}}</label>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">{{trans('login.button_login')}}</button>
                                </div>
                            </form>
                        </div>
<!--
                        <div class="row mb-4 mt-4">
                            <div class="col">
                                <hr class="m-2">
                            </div>
                            <div class="col-auto font-weight-lighter">{{ trans('login.text_divider_or') }}</div>
                            <div class="col">
                                <hr class="m-2">
                            </div>
                        </div>

                      <div class="row" id="app" data-type="login">
                            <div class="col-12">
                                <div class="text-center">
                                    <div class="spinner-grow m-3" role="status" v-if="false">
                                        <span class="sr-only">@lang('package.text_loading')</span>
                                    </div>
                                    <login-facebook></login-facebook>
                                </div>
                            </div>
                        </div>-->
                    </div>
                </div>
                <div class="text-center">
                    <span class="text-small">{{trans('login.hint_no_account')}} <a href="{{ route('packages') }}">{{trans('login.link_no_account')}}</a></span>
                </div>
            </div>
        </div>
    </div>
</section>
