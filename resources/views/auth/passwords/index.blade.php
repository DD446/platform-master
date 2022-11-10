@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('passwordchange') }}
@endsection

@section('content')

    <section class="bg-info text-light">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{ trans('auth.header_password_change') }}</h1>
                    <span class="title-decorative">{{trans('auth.subheader_password_change')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>

    </section>

    <section>
        <div class="card p-4 container">
            <div class="row">
                <div class="col-12 col-md-8 col-lg-7">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        <span class="error">{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('password.aendern') }}" method="post">
                        @csrf
                        @method('PUT')

                        @include('parts.fields.password', ['required' => true, 'label' => trans('auth.old_password'), 'name' => 'password_existing', 'large' => true, 'autofocus' => true, 'tabindex' => 1])
                        @include('parts.fields.password', ['required' => true, 'label' => trans('auth.new_password'), 'name' => 'password', 'large' => true, 'tabindex' => 2])
                        @include('parts.fields.password', ['required' => true, 'label' => trans('login.label_confirm_password'), 'name' => 'password_confirmation', 'large' => true, 'tabindex' => 3])

                        <div class="mt-3">
                            <button class="btn btn-danger btn-lg float-right" type="submit" tabindex="4">{{ trans('auth.button_save_password') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
