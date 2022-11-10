@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('video', $video) }}
@endsection

@section('content')
    <section class="space-md bg-gradient overflow-hidden">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 mb-5 mb-md-0 position-relative">
                    <h1 class="display-4">{{ $video->title }}</h1>
                    <p class="title-decorative">
                        {{ $video->subtitle }}
                    </p>
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section class="bg-white">
        <div class="container" id="app">
            <div class="row justify-content">
                <div class="col-12">
                    <b-embed type="video" aspect="4by3" controls autoplay :poster="'{{ $video->getLink('poster') }}'">
                        @if($video->webm)
                        <source src="{{$video->getLink('webm')}}" type="video/webm">
                        @endif
                        @if($video->mp4)
                        <source src="{{$video->getLink('mp4')}}" type="video/mp4">
                        @endif
                        @if($video->ogv)
                        <source src="{{$video->getLink('ogv')}}" type="video/ogg">
                        @endif
                    </b-embed>
                </div>
            </div>
            <!--end of row-->
            <div class="row pt-5">
                {!! $video->content !!}
            </div>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->

@endsection('content')

@push('scripts')
    <script src="{{ asset(mix('js1/jquery-3.6.0.min.js')) }}"></script>
    <script src="https://support.podcaster.de/assets/chat/chat.min.js"></script>
    <script>
        $(function() {
            const chat = new ZammadChat({
                background: '#212529',
                fontSize: '12px',
                chatId: 1,
                onConnectionEstablished(data) {
                    chat.send('chat_session_notice', {
                        session_id: chat.sessionId,
                        message: '{{auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name . ' [' . auth()->user()->email . '] (' . auth()->user()->usr_id . ')' : 'Gast' }}',
                    });
                }
            });
        });
    </script>
@endpush
