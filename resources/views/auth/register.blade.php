@extends('main')

@section('content')
<section class="space-sm">
    <div class="container">
        <div class="row mb-5 d-none d-md-block">
            <div class="col text-center">
                <a href="{{ route('home') }}">
                    <img alt="Podcast Hosting" src="{{ asset('images1/podcaster_logo_260x90_trans.png') }}"/>
                </a>
            </div>
            <!--end of col-->
        </div>
        <div class="row flex-md-row card card-lg">
            <div class="col-12 col-md-7 card-body bg-secondary">
                <div class="text-center mb-5">
                    <h1 class="h2 mb-2">Start creating immediately</h1>
                    <span>Delight your team and customers with Wingman</span>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9">
                        <form method="post" action="{{ route('register') }}">
                            @csrf
                            <div class="form-row form-group">
                                <div class="col">
                                    <input class="form-control form-control-lg" type="text" id="company" placeholder="Company Name" />
                                </div>
                            </div>
                            <div class="form-row form-group">
                                <div class="col">
                                    <input class="form-control form-control-lg" type="text" id="firstname" placeholder="First Name" />
                                </div>
                                <div class="col">
                                    <input class="form-control form-control-lg" type="text" id="lastname" placeholder="Last Name" />
                                </div>
                            </div>
                            <div class="form-row form-group{{ $errors->has('email') ? ' alert alert-danger' : '' }}">
                                <div class="col">
                                    <input class="form-control form-control-lg {{ $errors->has('email') ? ' invalid' : 'valid' }}" type="email" id="email" placeholder="{{ trans('login.placeholder_email') }}" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
{{--                            <div class="form-row form-group">
                                <div class="col">
                                    <input class="form-control form-control-lg" type="password" id="password" placeholder="Password" />
                                    <small>Password must be at least 7 characters</small>
                                </div>
                            </div>--}}
                            <div class="form-row form-group">
                                <div class="col">
                                    <button class="btn btn-block btn-success btn-lg" type="submit">{{ trans('login.button_register') }}</button>
                                </div>
                            </div>
                            <div class="text-center">
                                <span class="text-small text-muted">By clicking 'Create Account' you agree to our <a href="#">Terms</a>, <a href="#">Privacy Policy</a>&nbsp;and <a href="#">Security Policy</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end of col-->
            <div class="col-12 col-md-5 card-body">
                <div>
                    <div class="mb-5 text-center">
                        <h3 class="h2 mb-2">Features &amp; Benefits</h3>
                        <span>All plans come with 14 days free</span>
                    </div>
                    <ul class="list-unstyled list-spacing-sm mb-5 ">
                        <li class="row">
                            <div class="col-2 col-md-1"><i class="icon-check h5 text-muted"></i>
                            </div>
                            <div class="col-10">Introduce the major benefits of your product and set the scene for what's to come</div>
                        </li>
                        <li class="row align-items-center">
                            <div class="col-2 col-md-1"><i class="icon-check h5 text-muted"></i></div>
                            <div class="col-10">Make a bold new start today</div>
                        </li>
                        <li class="row">
                            <div class="col-2 col-md-1"><i class="icon-check h5 text-muted"></i>
                            </div>
                            <div class="col-10">Describe some key features of this aspect of the product</div>
                        </li>
                        <li class="row align-items-center">
                            <div class="col-2 col-md-1"><i class="icon-check h5 text-muted"></i>
                            </div>
                            <div class="col-10">Make a bold new start today</div>
                        </li>
                    </ul>

                    <div class="card card-sm box-shadow text-left">
                        <div class="card-body p-4">
                            <div class="media">
                                <img alt="Image" src="img/avatar-male-1.jpg" class="avatar avatar-xs" />
                                <div class="media-body">
                                    <p class="mb-1 text-small">
                                        “Let’s get one thing straight, this theme’s a straight-up winner. No posers here, just beautiful design and code.”
                                    </p>
                                    <small>Daniel Cameron</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="text-center">
            <span class="text-small">Already have an account? <a href="{{ url('/login') }}">Log in here</a></span>
        </div>
    </div>
    <!--end of container-->
</section>
<!--end of section-->
@endsection
