@extends('main')

{{--@section('breadcrumbs')
    {{ Breadcrumbs::render('packages_destroy') }}
@endsection--}}

@section('content')
    <div>
        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center">
                        <h1 class="display-4">{{trans('user.header_package_destroy')}}</h1>
                        <span class="title-decorative">{{trans('user.subheader_package_destroy')}}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        <section class="py-5 bg-white">
            <div class="bg-white">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            @include('feedback.form')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
