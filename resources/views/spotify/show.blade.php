@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('spotifystats') }}
@endsection

@section('content')
    <div class="container">

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col">
                        <h1 class="h2 mb-2">Spotify-Eintrag löschen</h1>
                        <span>Löscht einen Eintrag direkt im Spotify-Verzeichnis über das API</span>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->

        <section>
            <div class="row">
                <div class="col col-lg-12" id="app" data-type="spotifystats">
                    <form action="{{route('spotify.delete')}}" method="post">
                        @method('DELETE')
                        @csrf
                        <input type="text" name="spotifyUri" autofocus required value="{{$spotify}}">
                        <button type="submit" class="btn btn-primary">Eintrag löschen</button>
                    </form>
                </div>
            </div>
        </section>

    </div>
@endsection('content')
