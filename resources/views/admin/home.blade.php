@extends('main')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Admin-Bereich</h1>
            <p class="lead"></p>
        </div>
    </div>

    <section class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item"><a href="{{ route('nova.') }}">Backend</a></li>
                    <li class="list-group-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
                    <li class="list-group-item">
                        <form action="" method="post" onsubmit="this.action = document.getElementById('url').value + '/' + document.getElementById('user-id').value" target="_blank">
                            @csrf
                            @method('PUT')
                            <input type="hidden" value="{{ url('/admin/loginAs') }}" id="url">
                            <input type="number" id="user-id" placeholder="User-ID" required>
                            <input type="submit" value="Einloggen als Benutzer" class="btn btn-primary btn-sm">
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </section>
@endsection
