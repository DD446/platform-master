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
                Die/der <span class="font-weight-bold">Podcaster:in</span> produziert einen Podcast mit dem Namen <span class="font-weight-bold">{{ $feed_title ?? '' }}</span> unter der URL: <span class="font-weight-bold">{{ $feed_url ?? '"Wird nach Vertragsabschluss eingetragen!"' }}</span> und Collection-ID <span class="font-weight-bold">{{ $identifier ?? '"Wird nach Vertragsabschluss eingetragen!"' }}</span>  (???Podcast???). <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> schlie??t mit der/dem <span class="font-weight-bold">Podcaster:in</span> einen Kooperationsvertrag ??ber die Vermarktung des Podcasts und zur Vermarktung von Werbung durch Einbindung in den Podcast (???Werbeintegration???).
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Rechte und Pflichten von <span class="font-weight-bold">audio<span class="font-italic">takes</span></span>
                </span>
            </div>
            <div class="col-12">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> schlie??t mit Werbekunden im eigenen Namen und auf eigene Rechnung Vertr??ge nach Ma??gabe der gemeinsam festgelegten Anzeigentarife ??ber die Werbeintegrationen ab und erledigt die Umsetzung und Verwaltung dieser Vertr??ge einschlie??lich der Rechnungsstellung und des Mahnverfahrens eventuell ausstehender Rechnungsbetr??ge.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Rechte und Pflichten der/des <span class="font-weight-bold">Podcaster:in</span>
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> erlaubt <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> exklusiv die zeitlich f??r die Dauer dieses Vertrages befristete Werbeintegrationen, verbunden mit dem Recht, mit Werbekundenentsprechende Vertr??ge zu diesem Zweck abzuschlie??en. Eigene  Native Ads In  (selbsterstellte Sponsoren-Hinweise) sind von der Exklusivit??t nicht umfasst.
            </div>
            <div class="col-12 mt-2">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> versichert, dass keine strafrechtlich oder ethisch bedenkliche Werbung durchdie Werbeintegrationen mit der/dem <span class="font-weight-bold">Podcaster:in</span> geschaltet wird. Die/der <span class="font-weight-bold">Podcaster:in</span> sichert zu, dass auf dem Podcast keine Inhalte ver??ffentlicht werden, die gegen Gesetze versto??en oder die Rechte Dritter verletzen. Die/der  <span class="font-weight-bold">Podcaster:in</span>sichert zu, dass die Inhalte weder strafrechtlich, noch urheberrechtlich oder ethisch bedenklich oder sittenwidrig sind. Die/der <span class="font-weight-bold">Podcaster:in</span> aktiviert in seinen Podcast ??ber seinen Hoster die Werbeintegration, in dem er diesen Vermarktungsvertrag zustimmt. Hierf??r entstehen dem Podcaster f??r durch <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> vermarktete Werbefl??chen keine Kosten.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Verg??tung
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> erh??lt f??r die Vermarktung der Werbefl??chen {{ $share }}%  der Nettoeinnahmen, die <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> von Werbekunden aus der Schaltung von Werbung inden Werbeintegrationen erzielt. Als Nettoeinnahmen gelten die Bruttoeinnahmen abz??glich Umsatzsteuer, Agenturprovision, Skonti, Gutschriften und sonstigen gew??hrten Erm????igungen. <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> stellt hierf??r monatlich eine online abrufbare Auswertung zur Verf??gung. Der Verg??tungsanspruch des Podcasters entsteht, sobald Werbung in den Audiodateien der/des  <span class="font-weight-bold">Podcaster:in</span> geschaltet und vom Werbekunden bezahlt worden ist. Die Auszahlung der dem Podcaster zustehenden Verg??tung erfolgt monatlich, vorbehaltlich der Einhaltung aller vertraglich vereinbarten Pflichten. Die M??glichkeit der Auszahlung erfolgt ab einem Guthaben von 100 Euro.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Haftung
                </span>
            </div>
            <div class="col-12">
                <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> haftet nur bei Vorsatz oder grober Fahrl??ssigkeit. Ansonsten haftet <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> nur f??r den voraussehbaren vertragstypischen Schaden; sofern keine Kardinalpflichten verletzt sind, maximal bis zur H??he der/des <span class="font-weight-bold">Podcaster:in</span> zustehenden Verg??tung f??r die geschaltete Werbung, vorbehaltlich eines niedrigen Schadens. <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> haftet nicht f??r die Pflichtverletzung von Personen, die weder gesetzliche Vertreter noch leitende Angestellte sind, sofern keine wesentlichen Vertragspflichten verletzt wurden.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Gew??hrleistungen
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> bem??ht sich, die Podcasts, die Vertragsgegenstand sind, f??r die Dauer dieses Vertrages durchgehend funktionsf??hig und ??ffentlich verf??gbar zu halten, um die Werbeintegrationen zu erm??glichen. Die/der <span class="font-weight-bold">Podcaster:in</span> sichert zu, Inhaber aller Rechte zu sein, die zur Durchf??hrung dieses Vertrages erforderlich sind. Dazu geh??ren insbesondere die entsprechenden Nutzungsrechte an den Inhalten der Podcasts.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Freistellung
                </span>
            </div>
            <div class="col-12">
                Die/der <span class="font-weight-bold">Podcaster:in</span> stellt <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> von Anspr??chen Dritter frei, die auf einer Verletzung von Gew??hrleistungen von der/dem <span class="font-weight-bold">Podcaster:in</span>, von Rechten Dritter oder Gesetz-/Vertragsverletzungen durch die/den <span class="font-weight-bold">Podcaster:in</span>  oder dessen Erf??llungs- oder Verrichtungshilfen gegr??ndet sind und gegen <span class="font-weight-bold">audio<span class="font-italic">takes</span></span> geltend gemacht werden.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Vertragsbeginn, Vertragsdauer, Vertragsende
                </span>
            </div>
            <div class="col-12">
                Der Vertrag beginnt mit der Vertragsunterzeichnung und gilt unbefristet. Er kann unter Einhaltung einer K??ndigungsfrist von mindestens 6 Monaten zum Ende der Laufzeit (Jahresfristende) von einem der beiden Vertragspartner schriftlich gek??ndigt werden, erstmalig zum xx.xx.202x. Das Recht zur K??ndigung aus wichtigem Grund bleibt unber??hrt.
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <span class="font-weight-bolder">
                    Schlussbestimmungen
                </span>
            </div>
            <div class="col-12">
                Die Vertragsparteien begr??nden durch diesen Vertrag keine Gesellschaft. ??nderungen und Erg??nzungen dieses Vertrages bed??rfen der Schriftform. Dies gilt auch f??r die Abrede der Schriftform selbst. M??ndliche Nebenabreden bestehen nicht.
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
