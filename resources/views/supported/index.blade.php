@extends('main')

@section('content')
    <section class="container">
        <h1 class="display-3">@lang('supporter.header_supported')</h1>

        <form action="{{ route('supported.show') }}" method="post">
            @csrf
            <div class="form-group">
                <select name="supported_id" class="form-control form-control-lg">
                    @forelse($supported as $supporter)
                        <option value="{{ $supporter->user_id }}">{{ $supporter->user->first_name }} {{ $supporter->user->last_name }} &lt;{{ $supporter->user->email }}&gt;</option>
                    @empty
                        <option value="" disabled>@lang('supporter.no_supported')</option>
                    @endforelse
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary">Als Podcaster einloggen</button>
            </div>
        </form>
    </section>

@endsection('content')