@extends('main')

@section('content')
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
                            <h1 class="h2 mb-2">{{trans('login.header_reset_password')}}</h1>
                            <span>{{trans('login.subheader_send_reset_link')}}</span>
                        </div>
                        <div class="row no-gutters justify-content-center">
                            <form class="text-left col-lg-9" action="{{ route('password.email') }}" method="post">
                                @csrf
                                <div class="form-group{{ $errors->has('email') ? ' alert alert-danger' : '' }}">
                                    <label for="reset-email">{{trans('login.label_email')}}</label>
                                    <input class="form-control form-control-lg" type="email" name="email"
                                           id="reset-email" value="{{ old('email') }}"
                                           placeholder="{{trans('login.placeholder_reset_password')}}"
                                           required autofocus>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="text-center mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        {{trans('login.button_send_reset_link')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!--end of row-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
