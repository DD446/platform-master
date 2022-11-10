@inject('page', '\App\Models\Page')

@extends('main')

@section('title')
    <title>{{trans_choice('errors.title_code', 401, ['code' => 401])}}</title>
@endsection

@section('content')
    <section class="height-100 bg-dark">
        {{ $page->where('title', '=', 'errors')->first()->getFirstMedia('401') }}
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <i class="icon-compass display-4"></i>
                    <h1 class="h2">{{trans_choice('errors.header_code', 401, ['code' => 401])}}</h1>
                    <span>
                        @if($exception->getMessage())
                            {{  $exception->getMessage() }}
                        @else
                            {!! trans_choice('errors.message_error', 401, ['home'=> route('home'), 'contactus'=> route('contactus.create')]) !!}
                        @endif
                    </span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
