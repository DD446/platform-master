@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pat.header')}}</h1>
                    <span class="title-decorative">{{trans('pat.subheader')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>


    <section class="container">
        <div class="row title">
            <div class="small-12 column">
                <h6>{{trans('pages.header_imprint')}}</h6>
            </div>
        </div>

        <div class="row">
            <div class="large-12 column">
                <p>
                </p>
            </div>
        </div>

    </section>
@endsection
