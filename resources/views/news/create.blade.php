@extends('main')

@section('content')

    <section class="space-sm">
        {{--<img alt="Image" src="img/graphic-bg-clouds-5.png" class="bg-image" />--}}
        <div class="container">
            <div class="row mb-4 justify-content-center text-center">
                <div class="col-12 col-lg-12">
                    <h1 class="display-4">Nachricht @if($news->id) bearbeiten @else erstellen @endif</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="container">

        <div class="row">
            <div class="col-12 col-lg-12">

                <form action="@if($news->id) {{ route('news.update', ['id' => $news->id]) }} @else {{ route('news.store') }} @endif" method="post">
                    @csrf
                    @if($news->id)
                        <input type="hidden" name="_method" value="PUT">
                    @endif
                    @include('parts.fields.title', ['autofocus' => true, 'required' => true, 'value' => $news->title, 'label' => 'Titel'])
                    @include('parts.fields.textarea', ['name' => 'teaser', 'required' => true, 'value' => $news->teaser, 'label' => 'Teaser'])
                    @include('parts.fields.textarea', ['name' => 'body', 'required' => true, 'value' => $news->body, 'label' => 'News'])
                    {{--@include('parts.fields.textarea', ['name' => 'body', 'required' => true, 'value' => $news->body, 'label' => 'News'])--}}

                    <label class="label">News</label>
                    <div id="editor">
                        <p>{{ $news->body }}</p>
                    </div>

                    <button type="submit" class="btn btn-primary">@if($news->id) News aktualisieren @else News veröffentlichen @endif</button>
                </form>

            </div>
        </div>
    </section>

@endsection

@section('local_js')
<script type="text/javascript" src="{{ asset('js/ballooneditor.js') }}"></script>
<script>
    $(document).ready(function() {
        BalloonEditor
            .create( document.querySelector( '#editor' ), {
                plugins: [ 'Essentials', 'Paragraph', 'Bold', 'Italic', 'Heading', 'Link', 'Image', 'ImageUpload', 'CKFinderUploadAdapter', /*'BulletedList', 'NumberedList',*/ 'BlockQuote' ],
                ckfinder: {
                    uploadUrl: '/upload/image/news'
                }
            } )
            .then( editor => {
            } )
            .catch( err => {
                //console.error(err.stack);
            } );
    });
</script>
@endsection
@section('local_css')
<link type="text/css" rel="stylesheet" href="" />
@endsection