@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('contactus') }}
@endsection

@section('content')
{{--    <section class="space-lg bg-info text-light overflow-hidden">
        <img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/contactus_bg_1.jpg') }}" class="bg-image opacity-60" />
    </section>--}}

    <section class="space-sm bg-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('contact_us.header')}}</h1>
                    <span class="title-decorative">{{trans('contact_us.subheader')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="bg-transparent space-lg">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-12 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="card-body" data-type="contactus" id="app">
{{--                            @if ($errors->has('g-recaptcha-response'))
                                <div class="alert alert-danger">
                                    <ul>
                                        <li>{{ $errors->first('g-recaptcha-response') }}</li>
                                    </ul>
                                </div>
                            @endif--}}
                            {{--@include('parts.forms.contactus')--}}

                            <div class="text-center" v-if="false">
                                <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="sr-only">...</span>
                                </div>
                            </div>

                            <contactus
                                :form-route="'{{route('contactus.store')}}'"
                                :type="'{{ \request('type') }}'"
                                :comment="'{{ \request('comment') }}'"
                                :email-user="'{{ auth()->check() ? auth()->user()->email : '' }}'"
                                :name-user="'{{ auth()->check() ? trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) : '' }}'"></contactus>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-5 section-intro">
                    <h1 class="display-3">{{trans('contact_us.side_header')}}</h1>
                    <span class="lead">
                        {!! trans('contact_us.help_text') !!}
                    </span>
                    <h2 class="display-4">Telefon-Hotline</h2>
                    <p class="lead display-4">
                        030-549072654
                    </p>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection

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
