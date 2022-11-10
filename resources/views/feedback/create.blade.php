@extends('main')

@section('content')
    <div class="container">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">@lang('feedback.header')</h1>
                        <span>{{ trans_choice('feedback.subheader', $type) }}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section class="flush-with-above">
            <!--end of container-->
            @include('feedback.form')
        </section>
        <!--end of container-->

    </div>
@endsection
