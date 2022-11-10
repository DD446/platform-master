@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member_invitation') }}
@endsection

@section('content')
    <section class="bg-info text-light">
        <div class="container">
            <!--end of row-->
            <div class="row">
                <div class="col-12">
                    <h1 class="display-3">@lang('teams.header_member_invitation')</h1>
                    <span class="lead">
                        @lang('teams.subheader_member_invitation')
                    </span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section class="container" id="app" data-type="">

        <div class="row">
            <div class="col-12">
                <form action="{{route('invite.edit')}}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-12">
                            @include('parts.fields.firstname', ['required' => true, 'label' => trans('fields.firstname'), 'large' => true, 'autofocus' => true, 'tabindex' => 1])
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-12">
                            @include('parts.fields.lastname', ['required' => false, 'label' => trans('fields.lastname'), 'large' => true, 'tabindex' => 2])
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-12">
                            @include('parts.fields.password', ['required' => true, 'label' => trans('fields.password'), 'placeholder' => trans('teams.text_label_password'), 'large' => true, 'tabindex' => 3])
                        </div>
                    </div>
                    <div class="form-row form-group{{ $errors->has('terms') ? ' alert alert-danger' : '' }} text-center">
                        <label for="terms">
                            <input type="checkbox" id="terms" name="terms" value="on" required tabindex="4">
                            <span class="text-small text-muted">{!! trans('package.legal_text_links', ['route-terms' => route('page.terms'), 'route-privacy' => route('page.privacy')]) !!}</span><span>*</span>
                        </label>
                        @if ($errors->has('terms'))
                            <span class="help-block pt-2">
                                <strong>{{ $errors->first('terms') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-4 col-lg-6 col-12">
                            <button type="submit" class="btn btn-primary btn-lg" tabindex="5">{{trans('teams.text_button_save_member_data')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection('content')
