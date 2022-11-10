@auth
<div class="navbar-container">

    <b-navbar toggleable="lg" type="dark" variant="dark" id="nav" class="navbar">
        <b-navbar-brand href="{{ route('home') }}">
            <img src="{{asset('images1/podcaster_logo_weiss.svg')}}" alt="{{ config('app.name', 'podcaster') }}" style="height: 45px" height="45" width="128" />
        </b-navbar-brand>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav v-cloak>
            <b-navbar-nav>
                <b-nav-item href="{{ route('home') }}">{{ trans('nav.home') }}</b-nav-item>
                @if(auth()->guard('web')->check() && in_array(auth()->user()->role_id, [\App\Models\User::ROLE_USER, \App\Models\User::ROLE_SUPPORTER, \App\Models\User::ROLE_EDITOR]))
                    <b-nav-item href="{{ route('feeds') }}">{{ trans('nav.feeds') }}</b-nav-item>
                    <b-nav-item href="{{ route('mediathek.index') }}">{{ trans('nav.mediamanager') }}</b-nav-item>
                    <b-nav-item href="{{ route('stats') }}">{{ trans('nav.statistics') }}</b-nav-item>
                @else
                    @if(!auth()->guard('web')->check())
                    <b-nav-item href="{{ route('packages') }}">{{ trans('nav.packages') }}</b-nav-item>
                    @endif
                    <b-nav-item href="{{ route('lp.tour') }}">{{ trans('nav.tour') }}</b-nav-item>
                @endif

                <b-nav-item-dropdown
                    id="knowledge"
                    text="{{ trans('nav.knowledge') }}"
                >
                    <b-dropdown-item href="{{ url('/podcasting/16-podcast-erstellen-kostenloses-e-book') }}">
                        "Podcast erstellen" E-Book
                    </b-dropdown-item>
                    <b-dropdown-item href="{{ url('/podcasting/17-checklisten-fuer-podcast-interviews') }}">
                        Checklisten Remote-Interviews
                    </b-dropdown-item>
                    <b-dropdown-item href="{{ url('/podcasting/18-vorlage-podcast-konzept') }}">
                        Podcast Konzept Vorlage
                    </b-dropdown-item>
                    <b-dropdown-item href="{{ route('news.index') }}">
                        News
                    </b-dropdown-item>
                </b-nav-item-dropdown>
                <b-nav-item-dropdown
                    id="help"
                    text="{{ trans('nav.help') }}"
                    right
                >
                    <b-dropdown-item href="{{ route('faq.index') }}">
                        FAQ
                    </b-dropdown-item>
                    <b-dropdown-item href="{{ route('lp.videos') }}">
                        Videos
                    </b-dropdown-item>
                    <b-dropdown-item href="https://podster.de/c/podcast-hosting/6" target="_blank">
                        Forum
                    </b-dropdown-item>
                    <b-dropdown-item href="{{ route('contactus.create') }}">
                        Kontaktformular
                    </b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">
                @if(auth()->guard('web')->check())
                    <b-nav-item-dropdown
                            id="my-nav-dropdown"
                            text=""
                            toggle-class="nav-link-custom"
                            right
                    >
                        <template slot="button-content">
                            <em title="{{ auth()->user()->email }}, {{trans('package.package')}}: {{ trans_choice('package.package_name', auth()->user()->package->package_name) }}">
                                {{--<a href="/profil" class="text-light">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</a> &lt;<a href="/email" class="text-light">{{ auth()->user()->email }}</a>&gt (<a href="/pakete" class="text-light">{{ trans_choice('package.package_name', auth()->user()->package->package_name) }}</a>)--}}
                                {{ auth()->user()->first_name || auth()->user()->last_name ? trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) : auth()->user()->email }} ({{ trans_choice('package.package_name', auth()->user()->package->package_name) }})
                            </em>
                        </template>
                        @if(in_array(auth()->user()->role_id, [\App\Models\User::ROLE_USER, \App\Models\User::ROLE_SUPPORTER, \App\Models\User::ROLE_EDITOR]))
                            <b-dropdown-item href="{{ route('audiotakes.index') }}">
                                {{trans('nav.audiotakes')}}
                            </b-dropdown-item>
                            <b-dropdown-item href="{{ route('extras.index') }}">
                                {{trans('nav.package_extras')}}
                            </b-dropdown-item>
                            <b-dropdown-item href="{{ route('teams') }}">
                                {{trans('nav.contributors')}}
                            </b-dropdown-item>
                            <b-dropdown-item href="{{ route('dpa') }}">
                                {{trans('nav.dpa')}}
                            </b-dropdown-item>
                            <b-dropdown-divider></b-dropdown-divider>
                                <b-dropdown-item href="{{ url('/profil') }}">
                                    {{trans('nav.userprofile')}}
                                </b-dropdown-item>
                                <b-dropdown-item href="{{ route('accounting.index') }}">
                                    {{trans('nav.funds')}}
                                </b-dropdown-item>
                                <b-dropdown-item href="{{ route('rechnung.index') }}">
                                    {{trans('nav.bills')}}
                                </b-dropdown-item>
                            <b-dropdown-divider></b-dropdown-divider>
                        @endif
                        <b-dropdown-item v-on:click="logout">
                            {{trans('auth.logout')}}
                        </b-dropdown-item>
                    </b-nav-item-dropdown>
                    <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                        @csrf
                    </form>
                @else
                    <b-nav-item href="{{ route('login') }}">{{trans('nav.login')}}</b-nav-item>
                @endif
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</div>
@endauth
@guest
<div class="navbar-container">
    <nav class="navbar navbar navbar-dark bg-dark navbar-expand-lg" id="nav"><a href="/" target="_self" class="navbar-brand"><img src="/images1/podcaster_logo_weiss.svg" alt="podcaster.de" style="height: 45px; width: auto;"></a> <div id="nav-collapse" class="navbar-collapse collapse" style="display: none;"><ul class="navbar-nav"><li class="nav-item"><a href="/" target="_self" class="nav-link">Start</a></li> <li class="nav-item"><a href="/pakete" target="_self" class="nav-link">Pakete</a></li> <li class="nav-item"><a href="/tour" target="_self" class="nav-link">Tour</a></li>
                <li class="nav-item dropdown">
                    <a href="#" target="_self" class="nav-link hover-link">
                        Lösungen
                        <svg class="nav-dropdown-icon icon" aria-hidden="true" viewBox="0 0 14 14">
                            <polyline fill="none" stroke-width="1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="3.5,6.5 8,11 12.5,6.5 "></polyline>
                        </svg>
                    </a>
                    <div class="dropdown-menu double">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <h4 class="sector-header">Anforderungen</h4>
                                <a href="/podcasting/1-wie-ihr-coaching-geschaeft-von-podcasting-profitieren-kann" class="dropdown-item">
                                    Coaches &amp; Trainer
                                </a>
                                <a href="/podcasting/7-podcast-erstellen-fuer-gemeinden" class="dropdown-item">
                                    Gemeinden
                                </a>
                                <a href="/podcasting/6-podcasting-fuer-hochschulen" class="dropdown-item">
                                    Hochschulen &amp; Institute
                                </a>
                                <a href="/podcasting/9-podcast-erstellen-fuer-privatpersonen" class="dropdown-item">
                                    Privatpersonen
                                </a>
                                <a href="/podcasting/8-podcast-erstellen-fuer-selbststaendige-und-gruender" class="dropdown-item">
                                    Selbstständige &amp; Gründer
                                </a>
                                <a href="https://www.podcaster.de/podcasting/12-corporate-podcasts-erstellen" class="dropdown-item">
                                    Unternehmen
                                </a>
                                <a href="/podcasting/5-podcasting-fuer-verlage" class="dropdown-item">
                                    Verlage &amp; Online-Publisher
                                </a>
                            </div>
                            <div class="col-12 col-lg-6">
                                <h4 class="sector-header">Funktionen</h4>
                                <a href="/podcasting/10-interne-podcasts-erstellen" class="dropdown-item">
                                    Interne Podcasts
                                </a>
                                <a href="/podcasting/2-flexibler-podcast-web-player" class="dropdown-item">
                                    Webplayer
                                </a>
                                <a href="/podcasting/15-podcast-monetarisieren" class="dropdown-item">
                                    Podcast-Monetarisierung
                                </a>
                                <a href="/podcasting/14-podcast-tonqualitaet-einfach-verbessern-mit-auphonic" class="dropdown-item">
                                    Audio-Verbesserung
                                </a>
                                <a href="/podcasting/11-eigene-wordpress-website-zum-podcast-kostenlos-erstellen" class="dropdown-item">
                                    Webseite zum Podcast
                                </a>
                                <a href="/podcasting/13-podcast-rss-feed-als-website" class="dropdown-item">
                                    RSS-Feed als Website
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" target="_self" class="nav-link hover-link">
                        {{ trans('nav.knowledge') }}
                        <svg class="nav-dropdown-icon icon" aria-hidden="true" viewBox="0 0 14 14">
                            <polyline fill="none" stroke-width="1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="3.5,6.5 8,11 12.5,6.5 "></polyline>
                        </svg>
                    </a>
                    <div class="dropdown-menu">
                        <a href="/podcasting/16-podcast-erstellen-kostenloses-e-book" class="dropdown-item">
                            "Podcast erstellen" E-Book
                        </a>
                        <a href="{{ url('/podcasting/18-vorlage-podcast-konzept') }}" class="dropdown-item">
                            Podcast Konzept Vorlage
                        </a>
                        <a href="{{ url('/podcasting/17-checklisten-fuer-podcast-interviews') }}" class="dropdown-item">
                            Checklisten Remote-Interviews
                        </a>
                        <a href="{{ route('news.index') }}" class="dropdown-item">
                            News
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="/hilfe" target="_self" class="nav-link hover-link">
                        {{ trans('nav.help') }}
                        <svg class="nav-dropdown-icon icon" aria-hidden="true" viewBox="0 0 14 14">
                            <polyline fill="none" stroke-width="1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="3.5,6.5 8,11 12.5,6.5 "></polyline>
                        </svg>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('faq.index') }}" class="dropdown-item">
                            FAQ
                        </a>
                        <a href="{{ route('lp.videos') }}" class="dropdown-item">
                            Videos
                        </a>
                        <a href="https://podster.de" target="_blank" class="dropdown-item">
                            Forum
                        </a>
                        <a href="{{ route('contactus.create') }}" class="dropdown-item">
                            Kontaktformular
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto"><li class="nav-item"><a href="/login" target="_self" class="nav-link">Anmeldung</a></li></ul></div>
            <div id="nav-collapse" class="d-inline d-lg-none">
                <ul class="navbar-nav"><a href="/login" target="_self" class="nav-link">Anmeldung</a></ul>
            </div>
    </nav>
</div>
@endguest

<style>
    .navbar {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        background-color: #212529 !important;
    }
</style>
