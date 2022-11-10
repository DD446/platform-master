@extends('main')

@section('content')
<section class="space-sm">
    <div class="container align-self-start">
        <div class="row mb-5">
            <div class="col-md-8 col-md-offset-2">
                <div class="flex-center position-ref full-height">
                    <div class="content">
                        <div class="title m-b-md">
                            @auth
                                <a href="{{ url('/home') }}">{{trans('auth.dashboard')}}</a>

                                <a href="{{ route('logout') }}" class="dropdown-item"
                                   onclick="document.getElementById('logout-form').submit();return false;">
                                    {{trans('auth.logout')}}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                                    @csrf
                                </form>
                            @else
                                @include('auth.parts.login_form')
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection('content')