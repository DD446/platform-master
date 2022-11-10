@extends('main')

@section('content')
    <section class="container">
        <h1 class="display-3">@lang('supporter.header')</h1>

        <p>
            @lang('supporter.info')
        </p>

        <form action="{{ route('supporter.store') }}" method="post">
            @csrf
            <div class="form-group">
                <select name="supporter_id" class="form-control form-control-lg">
                    @foreach($supporters as $supporter)
                        <option value="{{ $supporter->usr_id }}">{{ $supporter->first_name }} {{ $supporter->last_name }} &lt;{{ $supporter->email }}&gt;</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary">Unterstützer hinzufügen</button>
            </div>
        </form>
    </section>

    <section class="container">
        <h3 class="display-3">@lang('supporter.header_remove')</h3>

        <ul class="list-group">
        @forelse($personals as $supporter)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $supporter->user->first_name }} {{ $supporter->user->last_name }} &lt;{{ $supporter->user->email }}&gt;
                <form action="{{ route('supporter.destroy', ['supporter' => $supporter->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <span class="badge badge-danger"><button class="btn btn-danger">Löschen</button></span>
                </form>
            </li>
        @empty
            @lang('supporter.no_personal_supporters')
        @endforelse
        </ul>
    </section>

@endsection('content')