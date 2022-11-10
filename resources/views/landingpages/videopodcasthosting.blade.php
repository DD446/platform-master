@extends('main')

@section('content')

    <section class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_videopodcasthost')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_videopodcasthost', ['service' => config('app.name')])}}</span>
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
                        <a href="https://kino.podcast.de/" title="Kino Aktuell nutzt das Video Podcast Hosting."><img src="/images/Content/site/video-podcast-hosting_300.png" alt="Video Podcast Hosting für Kino Aktuell" class="img float-right ml-3"></a> Mit Video Podcast Hosting bekommt jeder die Möglichkeit, seine <strong>selbst produzierten Bewegtbildinhalte</strong> Nutzern im Netz und als Download verfügbar zu machen. Wer seine Zuhörer auch zuschauen lassen möchte, um das Bild zum Ton zu liefern, kommt an einem Vodcast nicht vorbei.
                    </p>

                    <h3>Videopodcasting als Alternative zu YouTube</h3>

                    <p>
                        Während Fernsehsender schon seit Längerem ihre eigenen Sendungen aufzeichnen und als Videos ins Netz stellen, kann das mittlerweile jeder selbst. <strong>Video-Podcasting stellt hierbei eine interessante Ergänzung zu Youtube dar.</strong> Sie brauchen nicht einmal mehr eine Videokamera besitzen. Eine Webcam reicht.
                    </p>

                    <h3>Videos sind ressourcenhungrig</h3>

                    <p>
                        Das Filmen alleine reicht allerdings nicht aus, sollte aber das einzige sein, worauf sie sich konzentrieren müssen. Professionelles Hosting, genug Webspace und ausreichend Transfervolumen, um bei großer Nachfrage auch den Zugriff auf alle Inhalte für viele Nutzer zu ermöglichen, sollten sie einem <strong>professionellen Video-Hoster</strong> wie podcaster.de überlassen. Podcasting mit Videoinhalten benötigt weitaus mehr Ressourcen als das Podcasten nur mit Ton. Entsprechend stellt Video-Hosting im Vergleich zu Audio Podcast Hosting mehr Ansprüche an die Technik.
                    </p>

                    <h3>Videopodcast-Hosting von {{ config('app.name') }}</h3>

                    <p>
                        Das Maxi-Angebot bietet 2 Gigabyte Speicherplatz für ihre Videoinhalte und bis zu 5 Podcasts (RSS-Feeds). Der Speicherplatz steht ihnen jeden Monat neu zur Verfügung, ohne dass ihre alten Inhalte gelöscht werden. Und das Ganze zu einem Preis von nur 20 Euro monatlich! Darüber hinaus gehören Statistiken und ein <strong>Blog mit Video-Player</strong> zum Leistungsumfang. Zur freien Gestaltung des Blogs empfiehlt sich HTML5, was wir standardmäßig nutzen. Mit HTML5 kann sich jeder Kunde seinen Internetauftritt nach seinen eigenen Bedürfnissen entwerfen. Da Video Podcast Hosting sehr viel Übertragungsvolumen benötigt, ist dieses bereits im Preis inbegriffen. Bei den Videoformaten haben sie die Qual der Wahl. Unsere Empfehlung ist MP4 wegen der breiten Unterstützung bei der Hardware und weil es online wie offline von vielen Player abgespielt werden kann.
                    </p>

                    <h3>Alle Pakete stehen zur Wahl</h3>

                    <p>
                        Außer neuester Technik gehört auch herausragender Service zu den Eckpfeilern unseres professionellen Podcast Hosting. Unsere Angebote bieten im Kern die gleichen Leistungen, sind aber an verschiedenste Bedürfnisse angepasst und lassen sich dank der optional buchbaren Zusatzoptionen noch weiter individualisieren. Der Vergleich macht es deutlich: Wer Videoinhalte bereitstellen möchte, für den empfiehlt sich Vodcaster zum Podcasten. Mit dem <strong>monatlich neuen Speicherplatz</strong> können sie  viele Video-Inhalte produzieren, veröffentlichen und archivieren. Mit ihrem persönlichen Blog können die Nutzer auf die neuesten Episoden aufmerksam gemacht werden. Ein wichtiger Bestandteil vom Service ist das Support-Team. Sollte sich das Problem nicht mit einem Blick in die meistgestellten Fragen lösen lassen, dann hilft unser Team bei der schnellstmöglichen Lösung des Problems.
                    </p>

                    <div class="pt-5 align-content-center">
                        <a href="{{ route('package.order', ['id' => 4, 'name' => trans_choice('package.package_name', 'maxi')]) }}" class="btn btn-primary">Werde jetzt Videopodcaster</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection('content')
