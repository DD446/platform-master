@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_hoster')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_hoster', ['service' => config('app.name')])}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12 col-md-12">

                    <p>

                        <a href="https://www.podcaster.de/" title="Nutze uns als Podcast-Hoster!"><img src="/images/Content/site/podcast-hoster_300.png" alt="Podcast-Hoster" class="img float-right ml-3 mb-2"></a> Über einen Podcast können Abonnenten unabhängig von festen Sendezeiten Mediendateien (insbesondere Dateien für Audio und Video) in der Regel kostenlos abrufen. Dabei wird unter anderem das Datenformat MP3 verwendet, um die Inhalte auf Computern oder Playern abspielen zu können. Podcasten setzt voraus, dass der Produzent <strong>Webspace bei einem Podcast Host</strong> einrichtet. Ein Vergleich der verschiedenen Podcasting-Angebote lohnt sich, da sie sich in der Praxis erheblich voneinander unterscheiden: Wird auch ein Blog mit angeboten? Ist das Angebot bereits auf die zukünftigen Möglichkeiten von HTML5 zugeschnitten? Wächst der Speicherplatz kontinuierlich an und ist flexibel auf Sonderfolgen erweiterbar?

                    </p>

                    <h3>Podcast-Hoster mit Blog</h3>

                    <p>
                        Das Hosting von Podcasts erfordert nicht nur, dass der Podcast Hoster auf Dauer zuverlässig Medieninhalte (oft als MP3) im Bereich Audio und Video zur Verfügung stellt. Zudem muss ausreichend Webspace vorhanden sein, da Podcasten im Laufe der Zeit eine zunehmende Zahl an Dateien und damit eine stetig wachsende Datenmenge bedeutet. Ein Podcast-Betreiber, der nicht nur einfach <strong>Mediendateien ins Netz stellen</strong>, sondern das Podcasting mit weiteren Inhalten verknüpfen will, möchte oft auch einen Blog anbieten, der die einzelnen Episoden miteinander verbindet und einen inhaltlichen Zusammenhang herstellt. Dieser ist jedoch, wie ein Vergleich der Hosting-Anbieter zeigt, nicht immer kostenlos im gebuchten Tarif mit inbegriffen. Außerdem entwickelt sich das Internet weiter, Audio- oder Videoinhalte gelangen mit Standards wie HTML5 in Zukunft anders auf Player oder Computer als heute. Ein geeigneter Anbieter für das Hosting ist auch für kommende technische Entwicklungen gerüstet und macht damit das Podcasten zu einer langfristig unkomplizierten Angelegenheit, da die Anbieter von Podcasts bei Weiterentwicklungen nicht erst neue Programmiersprachen oder Anwendungen mühsam erlernen müssen.
                    </p>

                    <h3>Erfolgreich Podcasten</h3>

                    <p>
                        Podcasting ist eine geeignete Technik, um regelmäßig zu bestimmten Themen interessierten Abonnenten Mediendateien anzubieten und damit eine besonders enge Bindung zwischen Anbieter und Nutzer zu bewirken. Voraussetzung ist, dass der Betreiber über ausreichend Webspace verfügt, einen <strong>zuverlässigen Podcast Hoster</strong> ausgewählt hat sowie problemlos zusätzliche Inhalte, wie z. B. einen Blog, anbieten kann. Für ein zukünftig erfolgreiches Podcast-Angebot ist daher der richtige Podcast Hoster von ausschlaggebender Bedeutung und sollte gründlich vorbereitet werden, damit sich auf lange Sicht die Arbeit, die in ein solches Angebot investiert wird, auch lohnt.
                    </p>

                    <div class="pt-5 align-content-center">
                        <a href="{{ route('packages') }}" class="btn btn-primary">Wähle jetzt Dein Paket aus</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection('content')
