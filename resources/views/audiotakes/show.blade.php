@extends('main')

@section('content')
    <section class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6">
                <img src="{{ asset('/images1/podcast-pioniere-250px.png') }}" alt="Podcast Pioniere Logo" class="img img-fluid left mr-3" style="max-height: 85px">
            </div>
            <div class="col-12 col-sm-6 pt-3 pt-sm-0">
                <img src="{{ asset('/images1/audiotakes.svg') }}" alt="audiotakes Logo" class="img img-fluid right ml-0 ml-sm-3" style="max-height: 85px">
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <h3>
                    {{ trans('audiotakes.header_toc') }}
                </h3>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Zwischen
                </span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                audio<span class="font-italic">takes</span> GmbH
            </div>
            <div class="col-12">
                {{ trans('audiotakes.billing_company_address_street') }}
            </div>
            <div class="col-12">
                {{ trans('audiotakes.billing_company_address_city') }}
            </div>
            <div class="col-12">
                {{ trans('audiotakes.billing_company_address_country') }}
            </div>
        </div>

        <div class="row mt-2 text-center">
            <div class="col-12">
                im Folgenden <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> genannt
            </div>
            <div class="col-12">
                Ansprechpartner: Bastian Albert
            </div>
            <div class="col-12">
                E-Mail: bastian@audiotakes.net
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <span class="font-weight-bolder">
                    und
                </span>
            </div>
        </div>
        <div class="row mt-2">
            @isset($organisation)
            <div class="col-12">
                {{ $organisation ?? '' }}
            </div>
            @endisset
            <div class="col-12">
                {{ $first_name ?? '' }} {{ $last_name ?? '' }}
            </div>
            <div class="col-12">
                {{ $street ?? '' }} {{ $housenumber ?? '' }}
            </div>
            <div class="col-12">
                {{ $post_code ?? '' }} {{ $city ?? '' }}
            </div>
            <div class="col-12">
                {{ $country ?? '' ? Countries::getList(Str::before(app()->getLocale(), '-'))[$country] : '' }}
            </div>
        </div>

        <div class="row mt-2 text-center">
            <div class="col-12">
                im Folgenden <span class="font-weight-bold">Podcaster:in</span> genannt
            </div>
            <div class="col-12">
                Ansprechpartner/in: {{ $first_name ?? '' }} {{ $last_name ?? '' }}
            </div>
            <div class="col-12">
                E-Mail: {{ $email ?? '' }}
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Vertragsgegenstand
                </span>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> produziert einen Podcast mit dem Namen <span class="font-weight-bold">{{ $feed_title ?? '' }}</span> unter der URL: <span class="font-weight-bold">{{ $feed_url ?? '"Wird nach Vertragsabschluss eingetragen!"' }}</span> und Collection-ID <span class="font-weight-bold">{{ $identifier ?? '"Wird nach Vertragsabschluss eingetragen!"' }}</span>  („Podcast“). <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> schließt mit der/dem <span class="font-weight-bold">Podcaster:in</span> einen Kooperationsvertrag über die Vermarktung des Podcasts und zur Vermarktung von Werbung durch Einbindung in den Podcast („Werbeintegration“).
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Rechte und Pflichten von <span class="font-weight-bold">audio<span class="font-italic">takes</span></span>
                </span>
            </div>
            <div class="col-12">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> schließt mit Werbekunden im eigenen Namen und auf eigene Rechnung Verträge nach Maßgabe der gemeinsam festgelegten Anzeigentarife über die Werbeintegrationen ab und erledigt die Umsetzung und Verwaltung dieser Verträge einschließlich der Rechnungsstellung und des Mahnverfahrens eventuell ausstehender Rechnungsbeträge.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Rechte und Pflichten der/des <span class="font-weight-bold">Podcaster:in</span>
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> erlaubt <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> exklusiv die zeitlich für die Dauer dieses Vertrages befristete Werbeintegrationen, verbunden mit dem Recht, mit Werbekundenentsprechende Verträge zu diesem Zweck abzuschließen. Eigene  Native Ads In  (selbsterstellte Sponsoren-Hinweise) sind von der Exklusivität nicht umfasst.
            </div>
            <div class="col-12 mt-2">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> versichert, dass keine strafrechtlich oder ethisch bedenkliche Werbung durchdie Werbeintegrationen mit der/dem <span class="font-weight-bold">Podcaster:in</span> geschaltet wird. Die/der <span class="font-weight-bold">Podcaster:in</span> sichert zu, dass auf dem Podcast keine Inhalte veröffentlicht werden, die gegen Gesetze verstoßen oder die Rechte Dritter verletzen. Die/der  <span class="font-weight-bold">Podcaster:in</span>sichert zu, dass die Inhalte weder strafrechtlich, noch urheberrechtlich oder ethisch bedenklich oder sittenwidrig sind. Die/der <span class="font-weight-bold">Podcaster:in</span> aktiviert in seinen Podcast über seinen Hoster die Werbeintegration, in dem er diesen Vermarktungsvertrag zustimmt. Hierfür entstehen dem Podcaster für durch <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> vermarktete Werbeflächen keine Kosten.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Vergütung
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> erhält für die Vermarktung der Werbeflächen {{ $share }}%  der Nettoeinnahmen, die <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> von Werbekunden aus der Schaltung von Werbung inden Werbeintegrationen erzielt. Als Nettoeinnahmen gelten die Bruttoeinnahmen abzüglich Umsatzsteuer, Agenturprovision, Skonti, Gutschriften und sonstigen gewährten Ermäßigungen. <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> stellt hierfür monatlich eine online abrufbare Auswertung zur Verfügung. Der Vergütungsanspruch des Podcasters entsteht, sobald Werbung in den Audiodateien der/des  <span class="font-weight-bold">Podcaster:in</span> geschaltet und vom Werbekunden bezahlt worden ist. Die Auszahlung der dem Podcaster zustehenden Vergütung erfolgt monatlich, vorbehaltlich der Einhaltung aller vertraglich vereinbarten Pflichten. Die Möglichkeit der Auszahlung erfolgt ab einem Guthaben von 100 Euro.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Haftung
                </span>
            </div>
            <div class="col-12">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> haftet nur bei Vorsatz oder grober Fahrlässigkeit. Ansonsten haftet <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> nur für den voraussehbaren vertragstypischen Schaden; sofern keine Kardinalpflichten verletzt sind, maximal bis zur Höhe der/des <span class="font-weight-bold">Podcaster:in</span> zustehenden Vergütung für die geschaltete Werbung, vorbehaltlich eines niedrigen Schadens. <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> haftet nicht für die Pflichtverletzung von Personen, die weder gesetzliche Vertreter noch leitende Angestellte sind, sofern keine wesentlichen Vertragspflichten verletzt wurden.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Gewährleistungen
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> bemüht sich, die Podcasts, die Vertragsgegenstand sind, für die Dauer dieses Vertrages durchgehend funktionsfähig und öffentlich verfügbar zu halten, um die Werbeintegrationen zu ermöglichen. Die/der <span class="font-weight-bold">Podcaster:in</span> sichert zu, Inhaber aller Rechte zu sein, die zur Durchführung dieses Vertrages erforderlich sind. Dazu gehören insbesondere die entsprechenden Nutzungsrechte an den Inhalten der Podcasts.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Freistellung
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> stellt <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> von Ansprüchen Dritter frei, die auf einer Verletzung von Gewährleistungen von der/dem <span class="font-weight-bold">Podcaster:in</span>, von Rechten Dritter oder Gesetz-/Vertragsverletzungen durch die/den <span class="font-weight-bold">Podcaster:in</span>  oder dessen Erfüllungs- oder Verrichtungshilfen gegründet sind und gegen <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> geltend gemacht werden.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Vertragsbeginn, Vertragsdauer, Vertragsende
                </span>
            </div>
            <div class="col-12">
                Der Vertrag beginnt mit der Vertragsunterzeichnung und gilt unbefristet. Er kann unter Einhaltung einer Kündigungsfrist von mindestens 6 Monaten zum Ende der Laufzeit (Jahresfristende) von einem der beiden Vertragspartner schriftlich gekündigt werden, erstmalig zum xx.xx.202x. Das Recht zur Kündigung aus wichtigem Grund bleibt unberührt.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Schlussbestimmungen
                </span>
            </div>
            <div class="col-12">
                Die Vertragsparteien begründen durch diesen Vertrag keine Gesellschaft. Änderungen und Ergänzungen dieses Vertrages bedürfen der Schriftform. Dies gilt auch für die Abrede der Schriftform selbst. Mündliche Nebenabreden bestehen nicht.
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 col-sm-6">
                <div class="row">
                    <div class="col-12">
                        {{ $city ?? '' }}, {{ now()->isoFormat('DD.MM.YYYY') }}
                    </div>
                    <div class="col-12 mt-5">
                        -------------------------------------------------
                    </div>
                    <div class="col-12 mt-1">
                        <span class="font-weight-bold">Podcaster:in</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 pt-5 pt-sm-0">
                <div class="row">
                    <div class="col-12">
                        Berlin, {{ now()->isoFormat('DD.MM.YYYY') }}
                    </div>
                    <div class="col-12 mt-5">
                        -------------------------------------------------
                    </div>
                    <div class="col-12 mt-1">
                        <span class="font-weight-bold">audio<span class="font-italic">takes</span></span>
                    </div>
                </div>
            </div>
        </div>

    </section>

<!--<section class="container-fluid">
        <p>
            <a href="/" rel="download" target="_top">@lang('audiotakes.link_download_contract')</a>
        </p>
    </section>-->

@endsection('content')
