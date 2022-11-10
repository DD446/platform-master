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
            <form method="POST" action="{{ route('roulette.store') }}">
                @csrf

                <div class="form-group {{ $errors->has('feed_id') ? ' alert alert-danger' : '' }}">
                    <label for="feed">{{ trans('roulette.label_feed') }}*</label>
                    <select class="form-control" id="feed" required name="feed_id">
                        <option disabled>{{ trans('roulette.option_choose_feed') }}</option>
                        @foreach($feeds as $feed)
                            <option value="{{$feed->feed_id}}" @if(old('feed_id') == $feed->feed_id) selected @endif>{{$feed->rss['title']}}</option>
                        @endforeach
                    </select>
                    <div class="clearfix"></div>
                    <p class="help-block">{{ trans('roulette.help_feed') }}</p>
                </div>

                @include('parts.fields.textarea', [
                        'name' => 'podcasters',
                        'label' => trans('roulette.label_podcasters'),
                        'help' => trans('roulette.help_podcasters'),
                        'required' => true,
                        'text' => auth()->user()->first_name . ' ' . auth()->user()->last_name
                    ]
                )

                @include('parts.fields.email', [
                        'user' => auth()->user(),
                        'required' => true,
                        'help' => trans('roulette.help_email')
                    ])

                <div class="form-group {{ $errors->has('first_time') ? ' alert alert-danger' : '' }}">
                    <label for="ft" class="form-check-label">
                        <input type="checkbox" id="ft" name="first_time" class="form-check-inline" value="1" @if(old('first_time')) checked @endif>
                        {{ trans('roulette.label_first_time') }}
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary float-right">{{ trans('roulette.button_submit') }}</button>
                </div>
            </form>
        </div>
    </section>

@endsection('content')
