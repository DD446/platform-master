<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('title', new Illuminate\Support\HtmlString(SEO::generate(true)))

    <!-- Styles -->
    <link href="{{ asset(mix('css1/all.min.css')) }}" rel="stylesheet">
    <!-- Optional theme -->
    @stack('styles')
    @if(!isset($noTagManager))
{{--    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MBRCCT');</script>--}}
    <script>
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//mto.podcaster.de/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '6']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    @endif
</head>
<body>
    @if(!isset($noTagManager))
{{--    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBRCCT" height="0" width="0"
                      style="display:none;visibility:hidden"></iframe></noscript>--}}
    @endif
    @if(!isset($hideNav))
        @include('nav')
    @endif

    <div class="main-container">
        @hasSection('breadcrumbs')
        <nav aria-label="breadcrumb" role="navigation" class="bg-primary text-white">
            <div class="row">
                <div class="col ml-3">
                    @yield('breadcrumbs')
                </div>
            </div>
        </nav>
        @endif

        @if (session('status') || session('success') || session('error'))
            <section class="container" style="position:absolute;top:12em;z-index:3000;left:0;right:0">
                @if (session('status'))
                    <div class="alert alert-primary">
                        <button type="button" class="close float-right" aria-label="{{ trans('Close alert') }}" onclick="this.parentNode.remove()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('status') !!}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        <button type="button" class="close float-right" aria-label="{{ trans('Close alert') }}" onclick="this.parentNode.remove()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        <button type="button" class="close float-right" aria-label="{{ trans('Close alert') }}" onclick="this.parentNode.remove()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('error') }}
                    </div>
                @endif
            </section>
        @endif
        @yield('content')
        @if(!isset($hideNav))
            @include('announcement')
            @include('footer')
        @endif
    </div>

    <!-- Scripts -->
    @if((request()->route() && !in_array(request()->route()->getName(), ['home','lp.tour','news.index'.'page.team'.'page.press'])) || auth()->check())
    <script type="text/javascript" src="{{ asset(mix('js1/manifest.js')) }}"></script>
    <script type="text/javascript" src="{{ asset(mix('js1/vendor.js')) }}"></script>
    <script src="{{ asset(mix('js1/app.js')) }}"></script>
    @stack('scripts')
    @yield('footerscripts')
    @endif
</body>
</html>
