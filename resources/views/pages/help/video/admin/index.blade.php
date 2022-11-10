@extends('main')

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">Hilfe-Video-Admin</h1>
                    <span class="title-decorative"></span>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div>
            <h3>Alle Videos</h3>
            <form method="POST" action="{{ route('roulette.match.store') }}">
                @csrf
                @method('PUT')
                <ul>
                @foreach($videos as $video)
                    <li>
                        <div class="row">
                            <div class="col-auto">
                                {{$video->title}}
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('help.video.admin.edit', ['video' => $video->id]) }}">Bearbeiten</a>
                            </div>
                        </div>
                    </li>
                @endforeach
                <ul>
            </form>
        </div>
    </section>

@endsection('content')
