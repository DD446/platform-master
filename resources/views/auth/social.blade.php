@extends('main')

@section('content')
    <div id="app" data-type="social" class="mt-4">
        <div class="spinner-grow m-3" role="status" v-if="false">
            <span class="sr-only">@lang('package.text_loading')</span>
        </div>
        <alert-container></alert-container>
        <login-socialite :name="'{{ $name }}'"></login-socialite>
    </div>
@endsection