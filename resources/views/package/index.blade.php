@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('packages') }}
@endsection

@section('content')
    @auth
        @include('package.user')
    @else
        @include('package.guest')
    @endauth
@endsection
