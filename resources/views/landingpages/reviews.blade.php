@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('reviews') }}
@endsection

@section('content')

    <section class="space-md bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_reviews')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_reviews')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="space">
        <div class="container">
            <ul class="row feature-list feature-list-sm">
                @foreach($reviews as $review)
                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <a href="#">
                            <img class="card-img-top" src="{{ asset('storage/reviews/testamonials/' . $review['logo']) }}" alt="{{ $review['podcast'] }}">
                        </a>
                        <div class="card-body">
                            <h4 class="card-title">@lang('main.signature_cite', ['name' => $review['name'], 'podcast' => $review['podcast']])</h4>
                            <p class="card-text">{!! $review['cite'] !!}</p>
                        </div>
                    </div>
                </li>
                <!--end of col-->
                @endforeach
            </ul>
        </div>
        <!--end of container-->
    </section>
@endsection('content')
