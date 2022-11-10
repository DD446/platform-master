@extends('main')

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('roulette.header')}}</h1>
                    <span class="title-decorative">{{trans('roulette.subheader')}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div>
            <h3>Alle (offenen) Teilnehmer automatisch zuweisen (Zufall)</h3>
            <form method="POST" action="{{ route('roulette.match.store') }}">
                <input type="hidden" name="matching" value="random">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg">Losen</button>
                </div>
            </form>
        </div>

        <div class="pt-5">
            <h3>Teilnehmer händisch zuweisen (So Stef es will)</h3>
            <form method="POST" action="{{ route('roulette.match.store') }}">
                <input type="hidden" name="matching" value="selected">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            @error('roulette_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <label for="one">Podcaster</label>
                            <select name="roulette_id" id="one">
                                @foreach($unmatched as $player)
                                <option value="{{ $player->id }}">{{ $player->podcasters }} ({{ $player->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="one">Podcast-Partner</label>
                            <select name="roulette_partner_id" id="two">
                                @foreach($unmatched as $player)
                                    <option value="{{ $player->id }}">{{ $player->podcasters }} ({{ $player->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-lg">{{ trans('roulette.button_submit') }}</button>
                </div>
            </form>
        </div>

        <div class="pt-5">
            <h3>Roulette-Partner</h3>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="match"></label>
                            <select id="match">
                                @foreach($matches as $match)
                                    <option>{{ $match->player->podcasters }} ({{ $match->player->email }}) vs. {{ $match->partner->podcasters }} ({{ $match->partner->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
        </div>
    </section>

@endsection('content')
