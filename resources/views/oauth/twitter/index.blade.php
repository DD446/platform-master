@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('packages') }}
@endsection

@section('content')
    <section class="height-80 flush-with-above">
        <div class="container">
            <h3>Twitter</h3>

            <form action="{{ route('twitter.oauth') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">Bei Twitter anmelden</button>
            </form>
        </div>
    </section>
@endsection