@extends('main')

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('roulette.header_index', ['name' => config('app.name')])}}</h1>
                    <span class="title-decorative">{{trans('roulette.subheader_index')}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div>
            @if($pr)
                <div class="card">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="card-header">
                                <h3>
                                    {{ trans('roulette.header_participant') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        Podcast: {{$pr->feed}}
                                    </li>
                                    <li class="list-group-item">
                                        E-Mail: {{$pr->email}}
                                    </li>
                                    <li class="list-group-item">
                                        Teilnehmer: {{$pr->podcasters}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card-header">
                                <h3>
                                    {{ trans('roulette.header_partner') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($isMatched)
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            Podcast: {{ $partner->feed_title }}
                                        </li>
                                        <li class="list-group-item">
                                            E-Mail: {{ $partner->email }}
                                        </li>
                                        <li class="list-group-item">
                                            Teilnehmer: {{ $partner->podcasters }}
                                        </li>
                                    </ul>
                                @else
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                    <span class="text-warning">
                                        {{ trans('roulette.message_no_partner') }}
                                    </span>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($isMatched)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <h3>Promo-Material</h3>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <img src="https://bzjdis.podcaster.de/download/podcast-roulette-logo-1500px-quadratisch.jpg" alt="Logo" class="img img-fluid" style="max-height:300px">
                                <p class="mt-3">
                                    <a href="https://bzjdis.podcaster.de/download/podcast-roulette-logo-1500px-quadratisch.jpg" target="_top" download>Cover-Download</a>
                                </p>
                            </div>
                            <div class="col-6">
                                <audio src="https://bzjdis.podcaster.de/download/podcast-roulette_jingle-gesprochen.mp3" controls muted></audio>
                                <p class="mt-3">
                                    <a href="https://bzjdis.podcaster.de/download/podcast-roulette_jingle-gesprochen.mp3" target="_top" download>Jingle-Download</a>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-5">
                            @if ($match->file_id)
                                <div class="col-12">
                                    <h3>{{ trans('roulette.header_uploaded') }}</h3>
                                    <p class="alert alert-info">
                                        {{ trans('roulette.text_uploaded') }}
                                    </p>
                                    <p>
                                        <a href="https://www.podcast.de/podcast/1665559/podcast-roulette" target="_blank">{{ trans('roulette.follow_us') }}</a>
                                    </p>
                                </div>
                            @else
                            <div class="col-12">
                                <h3>{{ trans('roulette.header_upload') }}</h3>
                                <form action="{{ route('roulette.upload') }}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    @include('parts.fields.textarea', [
                                        'required' => true,
                                        'placeholder' => trans('roulette.placeholder_description'),
                                        'id' => 'description',
                                        'name' => 'description',
                                        'label' => trans('roulette.label_description'),
                                    ])

                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group{{ $errors->has('recording') ? ' alert alert-danger' : '' }}">
                                                <label for="recording">{{ trans('roulette.label_recording') }}*</label>
                                                <input type="file" class="form-control-file" id="recording" name="recording" accept="audio/mp3,audio/mpeg,.mp3">
                                                @error('recording')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group{{ $errors->has('cover') ? ' alert alert-danger' : '' }}">
                                                <label for="cover">{{ trans('roulette.label_cover') }}</label>
                                                <input type="file" class="form-control-file" id="cover" name="cover" accept="image/jpg,image/png,.jpg,.png">
                                                @error('cover')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary float-right">{{ trans('roulette.button_upload') }}</button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!$isMatched)
                    <div class="card-footer">
                        <form action="{{ route('roulette.destroy', ['id' => $pr->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger float-right">{{ trans('roulette.button_delete') }}</button>
                        </form>
                    </div>
                    @endif
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3>
                            {{ trans('roulette.header_register') }}
                        </h3>
                    </div>
                    <div class="card-body">
<!--                        <a href="{{ route('roulette.create') }}" class="btn btn-primary btn-lg">{{ trans('roulette.button_register') }}</a>-->
                        <p class="alert alert-warning">
                            {{ trans('roulette.message_registration_closed') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection('content')
