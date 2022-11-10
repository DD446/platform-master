@php
$hideNav = true;
@endphp
@inject('page', '\App\Models\Page')

@extends('main')

@section('title')
    <title>{{trans_choice('errors.title_code', 503, ['code' => 503])}}</title>
@endsection

@section('content')
    <section class="height-100 bg-dark">
        {{ $page->where('title', '=', 'errors')->first()->getFirstMedia('503') }}

        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <i class="icon-compass display-4"></i>
                    <h1 class="h2">{{trans_choice('errors.header_code', 503, ['code' => 503])}}</h1>
                    <span>{!! trans_choice('errors.message_error', 503, ['home'=> route('home'), 'contactus'=> route('contactus.create')]) !!}</span>
                    @if(File::exists(storage_path('framework/down')))
                        <br><br>
                        <strong>
                            {{ json_decode(file_get_contents(storage_path('framework/down')), true)['message'] }}
                        </strong>
                    @endif
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
