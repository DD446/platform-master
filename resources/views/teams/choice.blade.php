@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member_choice') }}
@endsection

@section('content')
    <section class="bg-info text-light">
        <div class="container">
            <!--end of row-->
            <div class="row">
                <div class="col-12">
                    <h1 class="display-3">@lang('teams.header_choice')</h1>
                    <span class="lead">
                        @lang('teams.subheader_choice')
                    </span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section class="container" id="app" data-type="projectlogin">

        <div class="row">
            <div class="col-12">

                <div class="text-center">
                    <div class="spinner-grow m-5 h-1" role="status" v-if="false">
                        <span class="sr-only">@lang('teams.is_loading')</span>
                    </div>
                </div>

                <project-login></project-login>
            </div>
        </div>
    </section>

@endsection('content')
