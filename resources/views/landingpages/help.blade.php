@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('help') }}
@endsection

@section('content')

    <section class="space-sm bg-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_help')}}</h1>
                    <span class="title-decorative">{{trans('pages.lead_help')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12 col-xl-6">
                    @include('faq.searchbar')
                </div>
                <div class="col-12 col-xl-6 mt-3 mt-lg-4 mt-xl-0">
                    <h3>Kontaktmöglichkeiten</h3>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <h2 class="">Formular</h2>
                            <p class="lead">
                                <a href="{{ route('contactus.create') }}">Kontaktiere uns</a>
                            </p>
                        </div>
                        <div class="col-12 col-md-3 mt-3 mt-sm-0">
                            <h2 class="">E-Mail</h2>
                            <p class="lead">
                                <a href="mailto:support@podcaster.de">support@podcaster.de</a>
                            </p>
                        </div>
                        <div class="col-12 col-md-3 mt-3 mt-sm-0">
                            <h2 class="">Telefon</h2>
                            <p class="lead">
                                <a href="tel:004930549072654">030-549072654</a>
                            </p>
                        </div>
                        <div class="col-12 col-md-3 mt-3 mt-sm-0">
                            <h2 class="">Forum</h2>
                            <p class="lead">
                                <a href="https://podster.de/c/podcast-hosting/6" target="_blank">Podster</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 col-lg-6">
                    <h3>Wissen nach Kategorien</h3>
                    <ul class="feature-list feature-list-sm row ml-4 ml-md-0">
                        @foreach($aCategories as $id => $attr)
                        <li class="mr-2" style="width:225px">
                            <a class="card text-center" href="{{ route('faq.category', ['id' => $id, 'slug' => Str::slug(trans_choice('faq.categories', $id))]) }}">
                                <div class="card-body">
                                    <i class="icon-{{ $attr['icon'] }} text-{{ $attr['color'] }} display-4"></i>
                                    <h6 class="title-decorative">{{ trans_choice('faq.categories', $id) }}</h6>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>Videos zum Dienst</h3>
                        </div>
                        <div>
                            <a href="{{ route('lp.videos') }}">
                                Alle Videos
                            </a>
                        </div>
                    </div>
                    <div id="app" data-type="help">
                        <help-videos-slider :videos="{{json_encode($videos)}}"></help-videos-slider>
                    </div>
                </div>
            </div>
            <!--end of row-->
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
