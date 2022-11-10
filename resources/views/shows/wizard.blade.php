@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('show_wizard') }}
@endsection

@section('content')

    <section class="bg-info text-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h1 class="h2 mb-2">{{ trans('feeds.header_show_wizard') }}</h1>
                    <h2 class="h5 mb-2">{{ trans('feeds.subheader_show_wizard') }}</h2>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section>
        <div class="container p-4 card">
            <h3>{{ trans('feeds.header_feeds') }}</h3>
            <ul class="list-group">
                @foreach($feeds as $feed)
                <li class="list-group-item">
                    <a href="{{route('show.create', ['id' => $feed->feed_id])}}">{{ $feed->rss['title'] }}</a>
                    {{--(<a href="/podcast/{{$feed->feed_id}}/episoden/#add/media" class="text-small">Alte Version</a>)--}}
                </li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection('content')
