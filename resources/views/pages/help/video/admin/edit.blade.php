@extends('main')

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">Hilfe-Video-Admin</h1>
                    <span class="title-decorative">{{ $video->page_title }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div>
            <h3>Video: {{ $video->title }}</h3>
            @if($errors->any())
                {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
            @endif
            <form method="POST" action="{{ route('help.video.admin.update', ['video' => $video->id]) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-auto">
                        <label>MP4</label>
                        <select name="mp4" class="form-control form-control-lg">
                            <option value=""></option>
                            @foreach($mp4s as $mp4)
                                <option @if($video->mp4 == $mp4->id) selected @endif  value="{{$mp4->id}}">{{$mp4->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <label>WEBm</label>
                        <select name="webm" class="form-control form-control-lg">
                            <option value=""></option>
                            @foreach($webms as $webm)
                                <option @if($video->webm == $webm->id) selected @endif value="{{$webm->id}}">{{$webm->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <label>OGV</label>
                        <select name="ogv" class="form-control form-control-lg">
                            <option value=""></option>
                            @foreach($ogvs as $ogv)
                                <option @if($video->ogv == $ogv->id) selected @endif value="{{$ogv->id}}">{{$ogv->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <label>Poster</label>
                        <select name="poster" class="form-control form-control-lg">
                            <option value=""></option>
                            @foreach($posters as $poster)
                                <option @if($video->poster == $poster->id) selected="selected" @endif value="{{$poster->id}}">{{$poster->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <input type="submit" class="btn btn-primary" value="Speichern">
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection('content')
