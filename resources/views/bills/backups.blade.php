@extends('main')

@section('content')

    <section>
        <div class="container card p-4">
            <ul>
                @foreach($files as $key => $bill)
                    <li>
                        <a href="/backup/bills/{{$key}}" target="_top">{{$bill}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

@endsection('content')
