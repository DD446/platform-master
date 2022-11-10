@extends('main')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Doppelte Episoden mit gleicher Guid löschen</h1>
            <p class="lead"></p>
        </div>
    </div>

    <section class="container">
        <div class="row">
            <div class="col-12">
                <form action="/feed/show/delete/duplicate" method="post">
                    @csrf
                    @method('DELETE')
                    <input type="text" name="user" placeholder="User-ID" required>
                    <input type="text" name="feed" placeholder="Feed-ID" required>
                    <input type="text" name="guid" placeholder="Guid" required>
                    <input type="submit" value="Duplikat löschen" class="btn btn-primary btn-sm">
                </form>
            </div>
        </div>
    </section>
@endsection
