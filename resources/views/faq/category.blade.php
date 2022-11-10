@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('faqcat', $id) }}
@endsection

@section('content')

    <section class="space-sm bg-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1 class="display-4">@isset($aFaq[0])<i class="icon-{{ $aFaq[0]->getCategoryAttributes($id)['icon'] }} text-{{ $aFaq[0]->getCategoryAttributes($id)['color'] }}"></i>@endisset {{trans_choice('faq.categories', $id)}}</h1>
                </div>
                <!--end of col-->
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    @include('faq.searchbar')
                </div>
            </div>
        </div>
    </section>
    <!--end of section-->

    <section class="">
        <div class="container bg-white p-5">
            <div class="row">
                <div class="col ml-2 mr-1">
                    <!--end of row-->
                    <ul class="row list-unstyled">
                        @foreach($aFaq as $faq)
                        <li class="space-xs">
                            <i class="icon-text-document text-muted mr-1"></i>
                            <a href="{{ route('faq.show', ['id' => $faq->faq_id, 'slug' => $faq->slug]) }}" class="">{{ $faq->question }}</a>
                            <p>
                                {!! Str::limit(strip_tags($faq->answer), $limit = 300, $end = ' ...') !!}
                            </p>
                        </li>
                        @endforeach
                    </ul>
                    <!--end of row-->

                    <div class="row">
                        {{ $aFaq->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
