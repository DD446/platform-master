@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('news') }}
@endsection

@section('content')

    <section class="space-sm bg-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('news.header_announcements')}}</h1>
                    <span class="title-decorative">{{trans('news.subheader_announcements')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="">
        <div class="container">
            @if($news->count() > 0)
            <ul class="row mb-3 feature-list feature-list-sm">
                @foreach($news as $story)
                <li class="col-12 col-md-6 col-lg-4">
                    <div class="card card-lg">
    {{--                    <a href="{{ route('news.show', ['id' => $story->id]) }}">
                            <img class="card-img-top" src="img/photo-team-desk.jpg" alt="Starting Up - A Candid Documentary">
                        </a>--}}
                        <div class="card-body">
                            <a href="{{ route('news.show', ['id' => $story->slug]) }}">
                                <h4 class="card-title mb-3">{{ $story->title }}</h4>
                            </a>
                            <p class="card-text">{!! nl2br($story->teaser) !!}</p>
                        </div>
                        <div class="card-footer card-footer-borderless">
                            <div class="media blog-post-author">
                                {{--<img alt="Image" src="img/avatar-male-1.jpg" class="avatar avatar-xs" />--}}
                                <div class="media-body">
                                    <small><a href="{{route('page.team')}}">{{$story->author}}</a></small>
                                    <small>
                                        {{\Carbon\Carbon::createFromTimeStamp(strtotime($story->created_at))->diffForHumans()}}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="card card-lg text-center">
                            <div class="card-body">
                                <i class="icon-add-to-list display-4 opacity-20"></i>
                                <h1 class="h5">{{ trans('news.no_news') }}</h1>
                            </div>
                        </div>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            @endif
        <!--end of row-->
{{--        <div class="row justify-content-center">
            <div class="col-auto">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                            <span aria-hidden="true"><i class="icon-chevron-left"></i>
                                            </span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                            <span aria-hidden="true"><i class="icon-chevron-right"></i>
                                            </span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>--}}
            {{ $news->links() }}
            <!--end of col-->
        </div>
        <!--end of container-->
    </section>
@endsection
