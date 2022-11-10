@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('faqitem', $faq) }}
@endsection

@section('content')
        <section class="bg-info text-light">
            <div class="container">
                @include('faq.searchbar')
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->
        <section class="bg-white">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="display-4">{{ $faq->question }}</h1>
                        {{--<span class="lead">A brief guide on getting started with the product API</span>--}}
{{--                        <div class="media blog-post-author">
                            <img alt="Fabio Bacigalupo" src="{{ asset('images1/fabio_bacigalupo_presse_klein.jpg') }}" class="avatar avatar-sm mr-3" />
                            <div class="media-body">
                                <span class="h6">Fabio Bacigalupo</span>
                                <span class="text-small">Gründer</span>
                            </div>
                        </div>--}}
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <!--end of section-->
        <section class="space-sm">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-12 col-md-8 col-lg-7">
                        <article id="faq-container">
                            {!! $faq->answer !!}
                        </article>
                        <hr>
                        <h5 class="mb-4">{{ trans('faq.header_likes') }}</h5>
                        <div class="d-flex justify-content-between">
                            @auth
                            <div id="app" data-type="faq">
                                <faq-like-button :likes="{{ $faq->likes }}" id="{{ $faq->faq_id }}"></faq-like-button>
                                <faq-like-button :likes="{{ $faq->dislikes }}" id="{{ $faq->faq_id }}" type="dislike"></faq-like-button>
                            </div>
                            @endauth
                            <div>
                                <a href="{{ route('contactus.create') }}" class="btn btn-outline-primary">{{ trans('faq.contact_support') }}</a>
                            </div>
                        </div>
                        @guest
                            <div class="mt-5 mb-2">
                                <a class="btn btn-info" href="{{route('packages')}}">Probiere jetzt podcaster.de kostenlos für 30 Tage aus!</a>
                            </div>
                        @endguest
                    </div>
                    <!--end of col-->
                    <div class="col-12 col-md-4">
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div><i class="icon-share mr-1"></i> {{ trans('faq.header_shares') }}</div>
                                        {{--<span>{{ $faq->shares ?? 0 }}</span>--}}
                                        {!!
                                            \Jorenvh\Share\ShareFacade::page(url()->current(), $faq->question, [], '<ul class="list-group list-group-horizontal list-group-flush list-group-shares">', '</ul>')
                                            ->facebook()
                                            ->twitter()
                                            ->linkedin(strip_tags($faq->answer))
                                            ->whatsapp()
                                            ->telegram()
                                            ->reddit();
                                        !!}
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div><i class="icon-edit mr-1"></i> {{ trans('faq.last_updated') }}</div>
                                        <span>
                                            {{$faq->last_updated->diffForHumans()}}
                                        </span>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div><i class="icon-text-document mr-1"></i> {{ trans('faq.created') }}</div>
                                        <span>
                                            {{$faq->date_created->diffForHumans()}}
                                        </span>
                                    </div>
                                </li>
    {{--                            <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div><i class="icon-thumbs-up mr-1"></i> Likes</div>
                                        <span>3</span>
                                    </div>
                                </li>--}}
                            </ul>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div>
                                    <span class="h6">{{trans_choice('faq.categories', $faq->category_id)}}</span>
                                    <span class="badge badge-secondary">{{ $cFaq }}</span>
                                </div>
                                <a href="{{ route('faq.category', ['id' => $faq->category_id, 'slug' => Str::slug(trans_choice('faq.categories', $faq->category_id))]) }}">{{ trans('faq.link_view_all') }} &rsaquo;</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled list-spacing-sm">
                                    @foreach($aFaq as $oFaq)
                                        <li>
                                            <i class="icon-text-document text-muted mr-1"></i>
                                            <a href="{{ route('faq.show', ['id' => $oFaq->faq_id, 'slug' => $oFaq->slug]) }}">{{ $oFaq->question }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!--end of card-->
                        <div class="card">
                            <div class="card-header">
                                <span class="h6">{{ trans('faq.header_categories') }}</span>
                            </div>
                            <div class="list-group list-group-flush">

                                @foreach($faq->getCategories() as $id => $attr)
                                <a class="list-group-item d-flex justify-content-between" href="{{ route('faq.category', ['id' => $id, 'slug' => Str::slug(trans_choice('faq.categories', $id))]) }}">
                                    <div>
                                        <i class="icon-{{ $attr['icon'] }} text-{{ $attr['color'] }} mr-1"></i>
                                        <span>{{ trans_choice('faq.categories', $id) }}</span>
                                    </div>
                                    <div>
                                        <i class="icon-chevron-right"></i>
                                    </div>
                                </a>
                                @endforeach

                            </div>
                        </div>
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
    <script>
    let useShare = function(link) {
        let popupSize = {
            width: 780,
            height: 550
        };

        let w=window,
            d=document,
            e=d.documentElement,
            g=d.getElementsByTagName('body')[0],
            x=w.innerWidth||e.clientWidth||g.clientWidth,
            y=w.innerHeight||e.clientHeight||g.clientHeight;

        let verticalPos = Math.floor((x - popupSize.width) / 2),
            horisontalPos = Math.floor((y - popupSize.height) / 2);

        let popup = window.open(link, 'social',
            'width=' + popupSize.width + ',height=' + popupSize.height +
            ',left=' + verticalPos + ',top=' + horisontalPos +
            ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

        if (popup) {
            popup.focus();
        }
        return false;
    };
    </script>
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
