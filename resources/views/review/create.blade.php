@extends('main')

@section('content')
    <section class="bg-dark text-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('review.header_create')}}</h1>
                    <span class="title-decorative">{{trans('review.subheader_create')}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                <span class="error">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('review.create') }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-12 col-md-6">
                        @include('parts.fields.textarea', [
                                'name' => 'q1',
                                'label' => 'Welches Problem wolltest du mit podcaster.de bewältigen?',
                                //'help' => trans('roulette.help_podcasters'),
                                'required' => false,
                                //'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                            ]
                        )
                    </div>
                    <div class="col-12 col-md-6">
                        @include('parts.fields.textarea', [
                                'name' => 'q2',
                                'label' => 'Welche Ergebnisse liefert dir podcaster.de?',
                                //'help' => trans('roulette.help_podcasters'),
                                'required' => false,
                                //'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                            ]
                        )
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        @include('parts.fields.textarea', [
                                'name' => 'q3',
                                'label' => 'Was gefällt dir an podcaster.de am Besten?',
                                //'help' => trans('roulette.help_podcasters'),
                                'required' => false,
                                //'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                            ]
                        )
                    </div>
                    <div class="col-12 col-md-6">
                        @include('parts.fields.textarea', [
                                'name' => 'q4',
                                'label' => 'Würdest du podcaster.de weiterempfehlen? Wenn ja, warum?',
                                //'help' => trans('roulette.help_podcasters'),
                                'required' => false,
                                //'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                            ]
                        )
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        @include('parts.fields.textarea', [
                                'name' => 'q5',
                                'label' => 'Liegt dir noch etwas auf dem Herzen?',
                                //'help' => trans('roulette.help_podcasters'),
                                'required' => false,
                                //'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                            ]
                        )
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mt-0 mt-lg-4">
                            <label for="review-permission">
                                <input type="checkbox" name="is_public" value="1" id="review-permission" checked class="form-check" />
                                Meine Rezension darf in Verbindung mit meinem Podcast und Vornamen komplett oder in Auszügen veröffentlicht werden.
                            </label>
                        </div>
                    </div>
                </div>


            <div class="form-group">
                <button type="submit" class="btn btn-primary float-right">{{ trans('review.button_create') }}</button>
            </div>
        </form>
    </div>
</section>

@endsection('content')
