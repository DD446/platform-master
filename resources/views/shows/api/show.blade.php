<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @yield('title', new Illuminate\Support\HtmlString(SEO::generate()))
    </head>
    <body>
        <h1>{{ $show->title }}</h1>
        <div>
            @isset($show->logo)
                <img src="{{$show->logo}}" alt="Logo zur Episode {{$show->title}}">
            @endisset
            {!! nl2br($show->description) !!}
        </div>
        @if($show->link)
        <div>
            <a href="{{$show->link}}" target="_blank"></a>
        </div>
        @endif
        @dump($show)
    </body>
</html>
