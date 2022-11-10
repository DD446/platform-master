@extends('main')

@section('content')
    <div class="container">

        <section class="flush-with-above">
            <div class="container">
                <h1>Meine Anfragen</h1>

                @forelse($invitations as $invitation)
                @empty

                @endforelse

                <a href="{{ route('invitations.create') }}" class="btn btn-primary btn-lg">Anfrage erstellen</a>
            </div>
        </section>
        <!--end of container-->
    </div>
@endsection