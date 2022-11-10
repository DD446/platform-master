@inject('news', '\App\Models\News')

@if($news->latest()->value('slug'))
<section class="space-xs text-lg-center bg-dark text-light">
    <div class="container">
        <div class="row">
            <div class="col">
                <i class="mr-1 icon-news"></i>
                <span class="mr-2"><a href="{{route('news.show', ['id' => $news->latest()->value('slug')])}}">{{ $news->latest()->value('title') }}</a></span>
                <a href="{{ route('news.index') }}" class="text-white">{{ trans('news.read_more') }} &rsaquo;</a>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
@endif
