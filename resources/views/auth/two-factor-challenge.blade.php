@extends('main')

@section('content')

<!--    AUTHENTICATION WITH 2FA SECRET CODE   -->
    <div class="container"  style="display: block;" id="secretCodeForm">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card my-4">
                    <div class="card-header"><h2>{{trans('profile.2fa_challenge')}}</h2></div>

                    <div class="card-body">
                        <p>{{trans('profile.enter_code')}}</p>

                        <form method="POST" action="/two-factor-challenge">
                            @csrf

                            <div class="row my-3">
                                <label for=" code"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Code') }}</label>

                                <div class="col-md-6">
                                    <input id="code" type="text"
                                           class="form-control @error('code') is-invalid @enderror" name="code"
                                           required autocomplete="current-code" autofocus>
                                    <small>
                                        {{trans('profile.no_device')}}
                                        <a href="#" onclick=callRecoveryForm()>{{trans('profile.use_recovery_code')}}</a>
                                    </small>


                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('profile.submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
            </div>
        </div>
    </div>




<!--    AUTHENTICATION WITH RECOVERY CODE   -->
    <div class="container" style="display: none;" id="recoveryCodeForm">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card my-4">
                    <div class="card-header"><h2>{{trans('profile.recovery_code')}}</h2></div>

                    <div class="card-body">
                        <p>{{trans('profile.enter_recovery_code')}}</p>

                        <form method="POST" action="/two-factor-challenge">
                            @csrf

                            <div class="row my-3">
                                <label for=" code"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Code') }}</label>

                                <div class="col-md-6">
                                    <input id="recovery_code" type="text"
                                           class="form-control @error('code') is-invalid @enderror" name="recovery_code"
                                           required autocomplete="current-code">
                                    <small>
                                        {{trans('profile.no_recovery')}}
                                        <a href="#" onclick=callCodeForm()>{{trans('profile.back_to_2fa')}}</a>
                                    </small>
                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('profile.submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function callRecoveryForm(){
            document.getElementById('recoveryCodeForm').style.display = "block";
            document.getElementById('secretCodeForm').style.display = "none";
            document.getElementById('recovery_code').focus();
        }
        function callCodeForm(){
            document.getElementById('recoveryCodeForm').style.display = "none";
            document.getElementById('secretCodeForm').style.display = "block";
            document.getElementById('code').focus();
        }
    </script>
@endpush