@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('approvals') }}
@endsection

@section('content')
    <div>
        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center">
                        <h1 class="display-4">{{trans('approvals.header')}}</h1>
                        <span class="title-decorative">{{trans('approvals.subheader')}}</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->

        </section>
    </div>

    <div class="container">
        <div class="row">

            <div class="col-12 mt-4" id="app" data-type="approvals">

                <div class="text-center">
                    <div class="spinner-grow m-3" role="status" v-if="false">
                        <span class="sr-only">@lang('package.text_loading')</span>
                    </div>
                </div>

                <alert-container></alert-container>
                <approvals
                    {{$hasAuphonicFeature ? ' can-use-auphonic ' : ''}}
                    :approvals="{{auth()->user()->approvals}}"></approvals>
            </div>

        </div>
        <!--end of row-->
    </div>
@endsection
