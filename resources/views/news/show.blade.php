@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('announcement', $news) }}
@endsection

@section('content')
    <div class="main-container">
        <section class="bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-7">
                        <h1 class="display-4">{{ $news->title }}</h1>
                        <span class="lead">{{ $news->teaser }}</span>
                        <div class="media blog-post-author">
                            <div class="media-body">
                                <span class="h6">{{$news->author}}</span>
                                <span class="text-small">
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime($news->created_at))->diffForHumans()}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->
        <section>
            <article class="container default-text">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        {!! nl2br($news->body) !!}
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </article>
            <!--end of container-->
        </section>
        <!--end of section-->
    </div>
@endsection
