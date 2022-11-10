@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('videos') }}
@endsection

@section('content')
    <section class="space-sm bg-gradient overflow-hidden">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_videos')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_videos')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="space-sm">
        <div class="container">
<!--            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between">
                    <div>
                        <span class="text-muted text-small">Results 1 - 12 of 25</span>
                    </div>
                    <form class="d-flex align-items-center">
                        <span class="mr-2 text-muted text-small text-nowrap">Sort by:</span>
                        <select class="custom-select">
                            <option value="alpha">Alphabetical</option>
                            <option value="old-new" selected="">Newest</option>
                            <option value="new-old">Popular</option>
                            <option value="recent">Recently Updated</option>
                        </select>
                    </form>
                </div>
            </div>-->
            <!--end of row-->
            <ul class="row feature-list feature-list-sm">
                @foreach($videos as $video)
                    <li class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <a href="{{ route('lp.video', ['video' => $video->id, 'slug' => Str::slug($video->title)]) }}">
                            <img class="card-img-top" src="{{ $video->getLink('poster') }}" alt="{{ $video->title }}">
                        </a>
                        <div class="card-body">
                            <a href="{{ route('lp.video', ['video' => $video->id, 'slug' => Str::slug($video->title)]) }}" class="text-reset">
                                <h4 class="card-title">{{ $video->title }}</h4>
                                <p class="card-text">{{ $video->subtitle }}</p>
                            </a>
                        </div>
<!--                        <div class="card-footer card-footer-borderless d-flex justify-content-between">
                            <div class="text-small">
                                <ul class="list-inline">
                                    <li class="list-inline-item"><i class="icon-heart"></i> 221</li>
                                    <li class="list-inline-item"><i class="icon-message"></i> 14</li>
                                </ul>
                            </div>
                            <div class="dropup">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-no-arrow" type="button" id="SidekickButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-dots-three-horizontal"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-sm" aria-labelledby="SidekickButton">
                                    <a class="dropdown-item" href="#">Save</a>
                                    <a class="dropdown-item" href="#">Share</a>
                                    <a class="dropdown-item" href="#">Comment</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Report</a>
                                </div>
                            </div>
                        </div>-->
                        <div class="row">
                            {{ $videos->links() }}
                        </div>
                    </div>
                </li>
                @endforeach
                <!--end of col-->
            </ul>
            <!--end of row-->

        </div>
        <!--end of container-->
    </section>

@endsection('content')
