@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('contributors') }}
@endsection

@section('content')
    <section class="bg-info text-light mb-4">
        <div class="container">
            <!--end of row-->
            <div class="row">
                <div class="col-12">
                    <h2 class="display-4">@lang('teams.header')</h2>
                    <span class="lead">
                        @lang('teams.subheader')
                    </span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>

        <!--end of container-->
    </section>

    <section class="container bg-white" id="app" data-type="teams">

        <div class="text-center">
            <div class="spinner-grow m-5 h-1" role="status" v-if="false">
                <span class="sr-only">@lang('package.text_loading')</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <alert-container></alert-container>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <members :allowed="{{json_encode($members)}}" {{ $canAddMembers ? 'can-add-members' : '' }}></members>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <project-login></project-login>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <h5>Feedback - Deine Meinung zählt!</h5>
                    <a href="{{ route('feedback.create') }}?type=8" class="btn btn-secondary"
                       onclick="window.open(this.href,'feature_teams','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0'); return false;">Rückmeldung
                        zum Mitarbeiter-Feature geben</a>
                </div>
            </div>
        </div>
    </section>
@endsection('content')
