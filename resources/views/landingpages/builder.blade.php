@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('lp', $lp) }}
@endsection

@section('content')

    <section class="space-xs bg-gradient">
        <div class="container default-text main-content">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{ $lp->title }}</h1>
                    <span class="title-decorative">{{ $lp->subtitle }}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="space">
        <div class="container bg-white p-4 default-text">
            {!! $lp->content !!}

            @includeIf('landingpages.extras.' . $lp->id, $extras ?? [])
            @include('landingpages.customers')
        </div>
        <!--end of container-->
    </section>

@endsection('content')
