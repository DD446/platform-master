@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('useremail') }}
@endsection

@section('content')

    <div>
        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">@lang('user.header_email')</h1>
                        <span>@lang('user.subheader_email')</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section>
            <div class="container bg-white p-3 p-lg-5 pb-5">

                @if($ueq)
                    <div class="col my-1">
                        <div class="row">
                            <div class="alert alert-info">
                                {{ trans('user.hint_email_change_requested', ['email' => $ueq->email]) }}
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{route('email.create')}}" method="post" class="" role="form">
                    @method('PUT')
                    @csrf

                            <div class="form-group{{ $errors->has('email') ? ' alert alert-danger' : '' }}">
                                <label for="oldemail" class="">@lang('user.label_old_email')</label>
                                <input type="email" name="email" readonly value="{{auth()->user()->email}}" required id="oldemail" class="form-control">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('newemail') ? ' alert alert-danger' : '' }}">
                                <label for="newemail" class="">@lang('user.label_new_email')</label>
                                <input type="email" name="newemail" value="{{ old('newemail') }}" required autofocus id="newemail" class="form-control">

                                @if ($errors->has('newemail'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('newemail') }}</strong>
                                        </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('newemail_confirmation') ? ' alert alert-danger' : '' }}">
                                <label for="newemailconfirmation" class="">@lang('user.label_new_email_confirmation')</label>
                                <input type="email" name="newemail_confirmation" value="{{ old('newemail_confirmation') }}" required id="newemailconfirmation" class="form-control">

                                @if ($errors->has('newemail_confirmation'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('newemail_confirmation') }}</strong>
                                        </span>
                                @endif
                    </div>

                    <button type="submit" class="btn btn-primary float-right">@lang('user.button_change_email')</button>

                </form>
            </div>
        </section>

    </div>
@endsection('content')
