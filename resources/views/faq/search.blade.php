@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('faqsearch', $query) }}
@endsection

@section('content')
    <section class="bg-info text-light">
        <div class="container">
            @include('faq.searchbar')
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section>
        <div class="container">
            <div class="row justify-content-center mb-1">
                <div class="col-auto">
                    <h1 class="h1">{{ trans('faq.search_results') }}</h1>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
    <section class="">
        <div class="container">
            <div class="col-12 col-md-8 col-lg-10">
                <ul class="row list-unstyled">
                    @forelse($aFaq as $faq)
                        <li class="space-xs">
                            <i class="icon-text-document text-muted mr-1"></i>
                            <a href="{{ route('faq.show', ['id' => $faq->faq_id, 'slug' => $faq->slug]) }}" class="">{{ $faq->question }}</a>
                            <p>
                                <span class="text-{{ $faq->getCategoryAttributes($faq->category_id)['color'] }}">{{ trans_choice('faq.categories', $faq->category_id) }}</span>
                                {!! Str::limit(strip_tags($faq->answer), $limit = 300, $end = ' ...') !!}
                            </p>
                        </li>
                    @empty
                        <li>
                            {{ trans('faq.search_no_results', ['query' => $query]) }}
                        </li>
                    @endforelse
                </ul>
                <div class="row">
                    {{ $aFaq->appends(['q' => $query])->links() }}
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->
@endsection
