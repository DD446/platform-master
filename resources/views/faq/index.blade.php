@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('faq') }}
@endsection

@section('content')
    <section class="bg-gradient text-light">
        {{--<img alt="{{ trans('main.background_image') }}" src="{{ asset('images1/faq_bg_1.jpg') }}" class="bg-image opacity-60" />--}}
        {{--{{ $page->getFirstMedia('bg') }}--}}

        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <span class="title-decorative">{{trans('faq.header_faq')}}</span>
                    <h1 class="display-4">{{trans('faq.subheader_faq')}}</h1>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            @include('faq.searchbar')
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section>
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto">
                    <h3 class="h4">{{trans('faq.header_browse_articles')}}</h3>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <ul class="feature-list feature-list-sm row">

                        @foreach($aCategories as $id => $attr)
                        <li class="col-6 col-md-4">
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
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section class="flush-with-above">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto">
                    <h3 class="h4">{{trans('faq.header_popular_articles')}}</h3>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <ul class="feature-list feature-list-sm row">

                @foreach($aFaq as $id => $faqs)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <span class="h6">{{ trans_choice('faq.categories', $id) }}</span>
                                {{--<span class="badge badge-secondary">3</span>--}}
                            </div>
                            <a href="{{ route('faq.category', ['id' => $id, 'slug' => Str::slug(trans_choice('faq.categories', $id))]) }}">{{trans('faq.link_view_all')}} &rsaquo;</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-spacing-sm">
                                @foreach($faqs as $faq)
                                <li>
                                    <i class="icon-text-document text-muted mr-1"></i>
                                    <a href="{{ route('faq.show', ['id' => $faq->faq_id, 'slug' => Str::slug($faq->question)]) }}">{{ $faq->question }}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--end of card-->
                </div>
                @endforeach
                <!--end of col-->

            </ul>
            <!--end of row-->
            <div class="row justify-content-center text-center section-outro">
                <div class="col-lg-4 col-md-5">
                    <h6>{{ trans('faq.header_answer_not_found') }}</h6>
                    <p>{{ trans('faq.questions_welcome') }}</p>
                    <a href="{{ route('contactus.create') }}">{{ trans('faq.contact_support') }} &rsaquo;</a>
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
