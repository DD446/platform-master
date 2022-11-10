@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('profile') }}
@endsection

@section('content')
    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('user.header')}}</h1>
                    <span class="title-decorative">{{trans('user.subheader')}}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card my-4">
                    <div class="card-header"><h2>{{ trans('profile.confirm_password') }}</h2></div>

                    <div class="card-body">
                        <p>{{ trans('profile.please_confirm') }}</p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="row my-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('profile.password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('profile.confirm_password') }}
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