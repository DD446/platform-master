@extends('main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('terms') }}
@endsection

@section('content')

    <section class="space-md bg-gradient">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-auto text-center">
                    <h1 class="display-4">{{trans('pages.header_terms')}}</h1>
                    <span class="title-decorative">{{trans('pages.subheader_terms')}}</span>
                </div>
                <!--end of col-->
            </div>
        </div>
    </section>

    <section class="container">

        <div class="row mb-3">
            <div class="col-12">
                <h2>I. Vertragsgegenstand, Änderungen</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p1">§1 Gegenstand der Nutzungsbedingungen</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Fabio Bacigalupo (nachfolgend „<em>Diensteanbieter</em>“ genannt) stellt auf <a href="{{ config('app.url') }}" title="Premium Medien-Hosting">www.{{ config('app.name') }}</a> eine Plattform zur Verfügung (nachfolgend „<em>das Portal</em>“ genannt), über welche die ordnungsgemäß angemeldeten Nutzer eigene Mediendateien hochladen können. Die Nutzer können auf dem Portal verfügbare Inhalte abrufen und die weiteren, auf dem Portal jeweils aktuell zur Verfügung stehenden unentgeltlichen sowie entgeltlichen Dienste im Rahmen der jeweiligen Verfügbarkeit nutzen. Nähere Informationen zu den Diensten finden sich in der Dienstebeschreibung, <a href="#p8">§8</a>.
                    </li>
                    <li>
                        Die vorliegenden Nutzungsbedingungen regeln die Zurverfügungstellung der Dienste durch den Diensteanbieter und die Nutzung dieser Dienste durch Sie als ordnungsgemäß angemeldeten Nutzer.
                    </li>
                    <li>
                        Informationen zum Diensteanbieter erhalten Sie <a href="{{ route('page.imprint') }}" title="Zum Impressum">hier</a>.
                    </li>
                </ol>
            </div>
        </div>


        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p2">§2 Änderungen der Nutzungsbedingungen</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                <li>
                    Der Diensteanbieter behält sich vor, diese Nutzungsbedingungen jederzeit mit Wirksamkeit auch innerhalb der bestehenden Vertragsverhältnisse zu ändern. Über derartige Änderungen wird der Diensteanbieter Sie mindestens 30 Kalendertage vor dem geplanten Inkrafttreten der Änderungen in Kenntnis setzen. Sofern Sie nicht innerhalb von 30 Tagen ab Zugang der Mitteilung widersprechen und die Inanspruchnahme der Dienste auch nach Ablauf der Widerspruchsfrist fortsetzen, so gelten die Änderungen ab Fristablauf als wirksam vereinbart. Im Falle Ihres Widerspruchs wird der Vertrag zu den bisherigen Bedingungen fortgesetzt. In der Änderungsmitteilung wird der Diensteanbieter Sie auf Ihr Widerspruchsrecht und auf die Folgen hinweisen.
                </li>
                <li>
                    Bei Änderungen der Umsatzsteuer ist der Diensteanbieter zu einer dieser Änderung entsprechenden Anpassung der Vergütung für den Premium-Zugang berechtigt, ohne dass das vorgenannte Widerspruchsrecht besteht.
                </li>
            </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>II. Anmeldung zur Nutzung, Umgang mit Zugangsdaten, Beendigung der Nutzung</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p3">§3 Anmeldeberechtigung</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Die Nutzung der auf dem Portal verfügbaren Dienste setzt Ihre Anmeldung als Nutzer voraus. Ein Anspruch auf die Nutzung besteht nicht. Der Diensteanbieter ist berechtigt, Nutzungsanträge ohne Angabe von Gründen zurückzuweisen.
                    </li>
                    <li>
                        Die Anmeldung ist Ihnen nur erlaubt, wenn Sie volljährig und unbeschränkt geschäftsfähig sind. Minderjährigen Personen ist eine Anmeldung untersagt. Bei einer juristischen Person muss die Anmeldung durch eine unbeschränkt geschäftsfähige und vertretungsberechtigte natürliche Person erfolgen.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p4">§4 Ihre Anmeldung auf dem Portal</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Die Registrierung erfolgt in der Weise (Double-Opt-In), dass der Nutzer mit der Bestellung eines bestimmten Leistungspakets ein eigenes Benutzerkonto zur Verfügung gestellt bekommt. Näheres zum jeweiligen Leistungsangebot finden Sie unter <a href="#p8">§8</a>.
                        <br /><br />
                        <strong>Bitte beachten Sie</strong>: Sofern die Beantragung des Zugangs zu einem Zweck erfolgt, der weder Ihrer gewerblichen noch Ihrer selbständigen beruflichen Tätigkeit zugerechnet werden kann, so steht Ihnen ein gesetzliches <strong>Widerrufsrecht</strong> zu.
                    </li>
                    <li>
                        Die während des Anmeldevorgangs vom Diensteanbieter erfragten Kontaktdaten und sonstigen Angaben müssen von Ihnen vollständig und korrekt angegeben werden. Bei der Anmeldung einer juristischen Person ist zusätzlich die vertretungsberechtigte natürliche Person anzugeben.
                    </li>
                    <li>
                        Nach Angabe aller erfragten Daten durch Sie werden diese vom Diensteanbieter auf Vollständigkeit und Plausibilität überprüft. Sind die Angaben aus Sicht des Diensteanbieters korrekt und bestehen aus Sicht des Diensteanbieters keine sonstigen Bedenken, schaltet der Diensteanbieter Ihr Benutzerkonto frei und benachrichtigt Sie hiervon per E-Mail. Die E-Mail gilt als Annahme Ihrer Bestellung.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p5">§5 Verantwortung für die Zugangsdaten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Im Verlauf des Anmeldevorgangs werden Sie gebeten, einen Benutzernamen und ein Passwort anzugeben. Mit diesen Daten können Sie sich nach der Freischaltung Ihres Zugangs und Ihrer Bestätigung gem. <a href="#p4">§4</a> III. auf dem Portal einloggen. Es liegt in Ihrer Verantwortung, dass der Benutzername nicht Rechte Dritter, insbesondere keine Namens- oder Markenrechte verletzt und nicht gegen die guten Sitten verstößt.
                    </li>
                    <li>
                        Die Zugangsdaten einschließlich des Passworts sind von Ihnen geheim zu halten und unbefugten Dritten nicht zugänglich zu machen.
                    </li>
                    <li>
                        Es liegt weiter in Ihrer Verantwortung sicher zu stellen, dass Ihr Zugang zu dem Portal und die Nutzung der auf dem Portal zur Verfügung stehenden Dienste ausschließlich durch Sie bzw. durch die von Ihnen bevollmächtigten Personen erfolgt. Steht zu befürchten, dass unbefugte Dritte von Ihren Zugangsdaten Kenntnis erlangt haben oder erlangen werden, ist der Diensteanbieter unverzüglich zu informieren.
                        <br /><br />
                        <strong>Sie haften für jedwede Nutzung und/oder sonstige Aktivität, die unter Ihren Zugangsdaten ausgeführt wird, nach den gesetzlichen Bestimmungen.</strong>
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p6">§6 Aktualisierung der Nutzerdaten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-4 pt-4">
                    Sie sind dazu verpflichtet, Ihre Daten (einschließlich Ihrer Kontaktdaten) aktuell zu halten. Tritt während der Dauer Ihrer Nutzung eine Änderung der angegebenen Daten ein, so haben Sie die Angaben unverzüglich auf dem Portal in Ihren persönlichen Einstellungen zu korrigieren. Sollte Ihnen dies nicht gelingen, so teilen Sie uns Ihre geänderten Daten bitte unverzüglich per E-Mail oder über unser <a href="{{ route('contactus.create') }}">Kontaktformular</a> mit.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p7">§7 Beendigung der Nutzung</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Für die Bestellung der einzelnen Leistungspakte gilt eine Kündigungsfrist von einem Monat.
                    </li>
                    <li>
                        Mit Wirksamwerden der Kündigung endet das Vertragsverhältnis und Sie dürfen Ihren Zugang nicht mehr nutzen. Der Diensteanbieter behält sich vor, den Benutzernamen sowie das Passwort mit Wirksamwerden der Kündigung zu sperren.
                    </li>
                    <li>
                        Der Diensteanbieter ist berechtigt, mit Ablauf von 30 Kalendertagen nach Wirksamwerden der Kündigung und nach Ablauf etwaiger gesetzlicher Vorhaltungsfristen sämtliche im Rahmen Ihrer Teilnahme entstandenen Daten unwiederbringlich zu löschen.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>III. Dienste und Inhalte auf dem Portal</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p8">§8 Diensteangebot und Verfügbarkeit der Dienste</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Der Diensteanbieter stellt Ihnen unterschiedliche Informations- und sonstige Dienste zur zeitlich befristeten Nutzung zur Verfügung. Solche Dienste können zB das Verfügbarmachen von Daten, Beiträgen, Bild- und Tondokumenten, Informationen und sonstigen Inhalten (nachfolgend zusammenfassend „<strong>Inhalte</strong>“ genannt) sein.
                        <br /><br />
                        Inhalt und Umfang der Dienste bestimmen sich nach den jeweiligen vertraglichen Vereinbarungen, im Übrigen nach den jeweils aktuell auf dem Portal verfügbaren Funktionalitäten.
                        <br /><br />
                        Auf dem Portal stehen Ihnen sowohl unentgeltliche als auch kostenpflichtige Dienste zur Verfügung. Kostenpflichtige Dienste sind jeweils als solche gekennzeichnet. Regelungen zu deren Inanspruchnahme finden Sie in <a href="#p12">§12</a>.
                    </li>
                    <li>
                        Jedem Nutzer steht ein bestimmtes Kontingent an Speicherplatz zur Verfügung. Der Speicherplatz wird monatlicht erneuert, wobei noch freier vorhandener Speicherplatz aus dem Vormonat nicht übertragen wird. Der Nutzer kann jedes Leistungspaket 30 Tage kostenlos testen. Nach Ablauf dieser 30tägigen Testphase wird die in Anspruch genommene Leistung kostenpflichtig. Die Zahlung wird über ein Guthabenkonto verrechnet. Das Benutzerkonto wird 14 Tage nach Ablauf der Testphase deaktiviert und sämtliche vom Nutzer hochgeladenen Mediendateien werden 30 Tage nach Ablauf der Testphase gelöscht.
                    </li>
                    <li>
                        Für sämtlichen kostenpflichtigen Dienste gewährleistet der Diensteanbieter in seinem Verantwortungsbereich eine Verfügbarkeit von 99% im Monatsmittel. Nicht in die Berechnung der Verfügbarkeit fallen die regulären Wartungsfenster des Webportals, die jeden Sonntag zwischen 2:00 und 4:00 Uhr liegen.
                        <br /><br />
                        Im Übrigen besteht ein Anspruch auf die Nutzung der auf dem Portal verfügbaren Dienste nur im Rahmen der technischen und betrieblichen Möglichkeiten beim Diensteanbieter. Der Diensteanbieter bemüht sich um eine möglichst unterbrechungsfreie Nutzbarkeit seiner Dienste. Jedoch können durch technische Störungen (wie zB Unterbrechung der Stromversorgung, Hardware- und Softwarefehler, technische Probleme in den Datenleitungen) zeitweilige Beschränkungen oder Unterbrechungen auftreten.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p9">§9 Änderungen von Diensten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-4 pt-4">
                    Der Diensteanbieter ist jederzeit berechtigt, auf dem Portal unentgeltlich bereitgestellte Dienste zu ändern, neue Dienste unentgeltlich oder entgeltlich verfügbar zu machen und die Bereitstellung unentgeltlicher Dienste einzustellen. Der Diensteanbieter wird hierbei jeweils auf Ihre berechtigten Interessen Rücksicht nehmen.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p10">§10 Schutz der Inhalte, Verantwortlichkeit für Inhalte Dritter</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Die auf dem Portal verfügbaren Inhalte sind überwiegend geschützt durch das Urheberrecht oder durch sonstige Schutzrechte und <strong>stehen jeweils im Eigentum des Diensteanbieters, der anderen Teilnehmer oder sonstiger Dritter, welche die jeweiligen Inhalte zur Verfügung gestellt haben</strong>. Die Zusammenstellung der Inhalte als solche ist ggf. geschützt als Datenbank oder Datenbankwerk iSd. §§ 4 Abs. 2, 87a Abs. 1 UrhG. Sie dürfen diese Inhalte lediglich gemäß diesen Nutzungsbedingungen sowie im auf dem Portal vorgegebenen Rahmen nutzen.
                    </li>
                    <li>
                        Die auf dem Portal verfügbaren Inhalte stammen teilweise vom Diensteanbieter und teilweise von anderen Teilnehmern bzw. sonstigen Dritten. Inhalte der Teilnehmer sowie sonstiger Dritter werden nachfolgend zusammenfassend „<strong>Drittinhalte</strong>“ genannt. Der Diensteanbieter führt bei Drittinhalten keine Prüfung auf Vollständigkeit, Richtigkeit und Rechtmäßigkeit durch und <strong>übernimmt daher keinerlei Verantwortung oder Gewährleistung für die Vollständigkeit, Richtigkeit, Rechtmäßigkeit und Aktualität der Drittinhalte</strong>. Dies gilt auch im Hinblick auf die Qualität der Drittinhalte und deren Eignung für einen bestimmten Zweck, und auch, soweit es sich um Drittinhalte auf verlinkten externen Webseiten handelt.
                        <br /><br />
                        Sämtliche Inhalte auf dem Portal sind Drittinhalte, ausgenommen diejenigen Inhalte, die mit einem Urheberrechtsvermerk des Diensteanbieters versehen sind.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>IV. Inanspruchnahme der Dienste auf dem Portal durch Sie</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p11">§11 Umfang der erlaubten Nutzung, Überwachung der Nutzungsaktivitäten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Ihre Nutzungsberechtigung beschränkt sich auf den Zugang zu dem Portal sowie auf die Nutzung der auf dem Portal jeweils verfügbaren Dienste im Rahmen der Regelungen dieser Nutzungsbedingungen.
                    </li>
                    <li>
                        Für die Schaffung der in Ihrem Verantwortungsbereich zur vertragsgemäßen Nutzung der Dienste notwendigen technischen Voraussetzungen sind Sie selbst verantwortlich. Der Diensteanbieter schuldet Ihnen keine diesbezügliche Beratung.
                    </li>
                    <li>
                        Der Diensteanbieter weist darauf hin, dass Ihre Nutzungsaktivitäten im gesetzlich zulässigen Umfang überwacht werden können. Dies beinhaltet ggf. auch die Protokollierung von IP-Verbindungsdaten und Gesprächsverläufen sowie deren Auswertungen bei einem konkreten Verdacht eines Verstoßes gegen die vorliegenden Teilnahme- und Nutzungsbedingungen und/oder bei einem konkreten Verdacht auf das Vorliegen einer sonstigen rechtswidrigen Handlung oder Straftat.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p12">§12 Inanspruchnahme kostenpflichtiger Dienste</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Der Diensteanbieter bietet auf dem Portal sowohl unentgeltliche als auch kostenpflichtige Dienste an. Auch das Abrufen von Inhalten (zB Bildschirmanzeige, Druck und/oder Download) kann Kosten auslösen. Die hierfür geltenden Preise entnehmen Sie bitte der Preisliste, die Sie nach Ihrer Anmeldung und ggf. hiernach in einer aktualisierten Fassung per E-Mail im PDF-Format erhalten haben.
                        <br /><br />
                        Soweit die Inanspruchnahme eines Dienstes (einschließlich des Abrufs von Inhalten) kostenpflichtig ist, erhalten Sie jeweils vor Eröffnung der Zugriffsmöglichkeit auf den jeweiligen Dienst online eine Mitteilung über die Ihnen entstehenden Kosten, die Zahlungsbedingungen und die weiteren relevanten Details. Erst hiernach haben Sie die Möglichkeit, den jeweiligen Dienst durch Mausklick auf den entsprechenden Button in Anspruch zu nehmen.
                        <br /><br />
                        <strong>Bitte beachten Sie</strong>: Mit Ihrem Klick auf den entsprechenden Button erklären Sie verbindlich, den jeweiligen Dienst in Anspruch nehmen zu wollen. <strong>Hierdurch nehmen Sie unser Angebot über die Zurverfügungstellung des kostenpflichtigen Dienstes verbindlich an, und es entsteht ein weiteres Vertragsverhältnis</strong>. Auch für dieses Vertragsverhältnis gelten die vorliegenden Teilnahme- und Nutzungsbedingungen, sowie ggf. weitere Bedingungen, über welche der Diensteanbieter Sie vor Inanspruchnahme des Dienstes informieren wird.
                        <br /><br />
                        Wenn Sie den kostenpflichtigen Dienst nicht in Anspruch nehmen möchten, so kehren Sie durch Klick auf den entsprechenden Button oder durch den „<em>zurück</em>“-Button Ihres Browsers zu den unentgeltlichen Diensten zurück.
                    </li>
                    <li>
                        <strong>Bitte beachten Sie weiter</strong>: Sie veranlassen die Zurverfügungstellung des kostenpflichtigen Dienstes unmittelbar durch Ihren Klick auf den entsprechenden Button. Da die Inanspruchnahme des Dienstes hiernach nicht rückgängig gemacht werden kann und eine Rücksendung von etwaig abgerufenen Inhalten von Ihnen nicht derart gewährleistet werden kann, dass Ihnen eine Nutzung nach Rückgabe nicht mehr möglich ist, <strong>steht Ihnen ein Widerrufsrecht bzgl. der Inanspruchnahme kostenpflichtiger Dienste nicht zu</strong>.
                    </li>
                    <li>
                        Sämtliche angegebenen Entgelte verstehen sich einschließlich der jeweils geltenden gesetzlichen Mehrwertsteuer.
                    </li>
                    <li>
                        Das Entgelt für die von Ihnen in Anspruch genommenen kostenpflichtigen Dienste wird Ihrem Guthabenkonto belastet. Die Rechnungen für die auf das Guthabenkonto eingezahlten Beträge können Sie in Ihrem persönlichen Bereich abrufen.
                    </li>
                    <li>
                        Bei Verzug ist der Diensteanbieter berechtigt, bei Verbrauchern Verzugszinsen in Höhe von 5 % über dem Basiszinssatz und bei Unternehmern Verzugszinsen in Höhe von 8 % über dem Basiszinssatz zu verlangen, wenn der Teilnehmer nicht einen geringeren oder der Diensteanbieter einen höheren Schaden nachweist.
                    </li>
                    <li>
                        Die Aufrechnung ist Ihnen nur mit unbestrittenen oder rechtskräftig festgestellten Gegenforderungen erlaubt. Ein Zurückbehaltungsrecht können Sie nur geltend machen, wenn es auf demselben Vertragsverhältnis beruht.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p13">§13 Einstellen von eigenen Inhalten durch Sie</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Soweit als Funktionalität auf dem Portal verfügbar, dürfen Sie unter Beachtung der nachfolgenden Regelungen Inhalte auf dem Portal einstellen und damit für Dritte verfügbar machen.
                    </li>
                    <li>
                        Mit dem Einstellen von Inhalten räumen Sie dem Diensteanbieter jeweils ein unentgeltliches und übertragbares Nutzungsrecht an den jeweiligen Inhalten ein, insbesondere
                        <ol>
                            <li>
                                zur Speicherung der Inhalte auf dem Server des Diensteanbieters sowie deren Veröffentlichung, insbesondere deren öffentlicher Zugänglichmachung (zB durch Anzeige der Inhalte auf dem Portal),
                            </li>
                            <li>
                                zur Bearbeitung und Vervielfältigung, soweit dies für die Vorhaltung bzw. Veröffentlichung der jeweiligen Inhalte erforderlich ist, und
                            </li>
                            <li>
                                zur Einräumung von – auch entgeltlichen – Nutzungsrechten gegenüber Dritten an Ihren Inhalten entsprechend <a href="#p14">§14</a>.
                            </li>
                        </ol>
                        Soweit Sie die von Ihnen eingestellten Inhalte wieder von dem Portal herunternehmen, erlischt das uns vorstehend eingeräumte Nutzungs- und Verwertungsrecht. Wir bleiben jedoch berechtigt, zu Sicherungs- und/oder Nachweiszwecken erstellte Kopien aufzubewahren. Die den Teilnehmern an von Ihnen eingestellten Inhalten bereits eingeräumten Nutzungsrechte bleiben ebenfalls unberührt.
                    </li>
                    <li>
                        <strong>Sie sind für die von Ihnen eingestellten Inhalte voll verantwortlich</strong>. Der Diensteanbieter übernimmt keine Überprüfung der Inhalte auf Vollständigkeit, Richtigkeit, Rechtmäßigkeit, Aktualität, Qualität und Eignung für einen bestimmten Zweck.
                        <br /><br />
                        <strong>Sie erklären und gewährleisten gegenüber dem Diensteanbieter daher, dass Sie der alleinige Inhaber sämtlicher Rechte an den von Ihnen auf dem Portal eingestellten Inhalten sind, oder aber anderweitig berechtigt sind (zB durch eine wirksame Erlaubnis des Rechteinhabers), die Inhalte auf dem Portal einzustellen und die Nutzungs- und Verwertungsrechte nach dem vorstehenden Absatz II. zu gewähren.</strong>
                    </li>
                    <li>
                        Der Diensteanbieter behält sich das Recht vor, das Einstellen von Inhalten abzulehnen und/oder bereits eingestellte Inhalte (einschließlich privater Nachrichten und Gästebucheinträge) ohne vorherige Ankündigung zu bearbeiten, zu sperren oder zu entfernen, sofern das Einstellen der Inhalte durch den Teilnehmer oder die eingestellten Inhalte selbst zu einem Verstoß gegen <a href="#p15">§15</a> geführt haben oder konkrete Anhaltspunkte dafür vorliegen, dass es zu einem schwerwiegenden Verstoß gegen <a href="#p15">§15</a> kommen wird. Der Diensteanbieter wird hierbei jedoch auf Ihre berechtigten Interessen Rücksicht nehmen und das mildeste Mittel zur Abwehr des Verstoßes gegen <a href="#p16">§16</a> wählen.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p14">§14 Nutzungsrecht an auf dem Portal verfügbaren Inhalten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Soweit nicht in diesen Nutzungsbedingungen oder auf dem Portal eine weitergehende Nutzung ausdrücklich erlaubt oder auf dem Portal durch eine entsprechende Funktionalität (zB Download-Button) ermöglicht wird,
                        <ol>
                            <li>
                                dürfen Sie die auf dem Portal verfügbaren Inhalte ausschließlich für persönliche Zwecke online abrufen und anzeigen. Dieses Nutzungsrecht ist auf die Dauer Ihrer vertragsgemäßen Teilnahme an dem Portal beschränkt;
                            </li>
                            <li>
                                ist es Ihnen untersagt, die auf dem Portal verfügbaren Inhalte ganz oder teilweise zu bearbeiten, zu verändern, zu übersetzen, vorzuzeigen oder vorzuführen, zu veröffentlichen, auszustellen, zu vervielfältigen oder zu verbreiten. Ebenso ist es untersagt, Urhebervermerke, Logos und sonstige Kennzeichen oder Schutzvermerke zu entfernen oder zu verändern.
                            </li>
                        </ol>
                    </li>
                    <li>
                        Zum Herunterladen von Inhalten („<em>Download</em>“) sowie zum Ausdrucken von Inhalten sind Sie nur berechtigt, soweit eine Möglichkeit zum Download bzw. zum Ausdrucken auf dem Portal als Funktionalität (zB mittels eines Download-Buttons) zur Verfügung steht.
                        <br /><br />
                        An den von Ihnen ordnungsgemäß herunter geladenen bzw. ausgedruckten Inhalten erhalten Sie jeweils ein zeitlich unbefristetes und nicht ausschließliches Nutzungsrecht für die Nutzung zu eigenen, nichtkommerziellen Zwecken. Soweit es sich um Inhalte handelt, die Ihnen im Rahmen der Basis-Mitgliedschaft entgeltlich überlassen werden, ist weitere Voraussetzung für diese Rechteeinräumung die vollständige Bezahlung der jeweiligen Inhalte. Im Übrigen verbleiben sämtliche Rechte an den Inhalten beim ursprünglichen Rechteinhaber (dem Diensteanbieter oder dem jeweiligen Dritten).
                    </li>
                    <li>
                        Ihre zwingenden gesetzlichen Rechte (einschließlich der Vervielfältigung zum privaten und sonstigen eigenen Gebrauch nach § 53 UrhG) bleiben unberührt.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p15">§15 Verbotene Aktivitäten</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        <strong>Ihnen sind jegliche Aktivitäten auf bzw. im Zusammenhang mit dem Portal untersagt, die gegen geltendes Recht verstoßen, Rechte Dritter verletzen oder gegen die Grundsätze des Jugendschutzes verstoßen</strong>. Insbesondere sind Ihnen folgende Handlungen untersagt:
                        <ol>
                            <li>
                                das Einstellen, die Verbreitung, das Angebot und die Bewerbung pornografischer, gegen Jugendschutzgesetze, gegen Datenschutzrecht und/oder gegen sonstiges Recht verstoßender und/oder betrügerischer Inhalte, Dienste und/oder Produkte;
                            </li>
                            <li>
                                die Verwendung von Inhalten, durch die andere Teilnehmer oder Dritte beleidigt oder verleumdet werden;
                            </li>
                            <li>
                                die Nutzung, das Bereitstellen und das Verbreiten von Inhalten, Diensten und/oder Produkten, die gesetzlich geschützt oder mit Rechten Dritter (zB Urheberrechte) belastet sind, ohne hierzu ausdrücklich berechtigt zu sein.
                            </li>
                        </ol>
                    </li>
                    <li>
                        Des Weiteren sind Ihnen auch unabhängig von einem eventuellen Gesetzesverstoß bei der Einstellung eigener Inhalte auf dem Portal sowie bei der Kommunikation mit anderen Teilnehmern <strong>die folgenden Aktivitäten untersagt</strong>:
                        <ol>
                            <li>
                                die Verbreitung von Viren, Trojanern und anderen schädlichen Dateien;
                            </li>
                            <li>
                                die Versendung von Junk- oder Spam-Mails sowie von Kettenbriefen;
                            </li>
                            <li>
                                die Verbreitung anzüglicher, anstößiger, sexuell geprägter, obszöner oder diffamierender Inhalte bzw. Kommunikation sowie solcher Inhalte bzw. Kommunikation die geeignet sind/ist, Rassismus, Fanatismus, Hass, körperliche Gewalt oder rechtswidrige Handlungen zu fördern bzw. zu unterstützen (jeweils explizit oder implizit);
                            </li>
                            <li>
                                die Belästigung anderer Teilnehmer, zB durch mehrfaches persönliches Kontaktieren ohne oder entgegen der Reaktion des anderen Teilnehmers sowie das Fördern bzw. Unterstützen derartiger Belästigungen;
                            </li>
                            <li>
                                die Aufforderung anderer Teilnehmer zur Preisgabe von Kennwörtern oder personenbezogener Daten für kommerzielle oder rechts- bzw. gesetzeswidrige Zwecke;
                            </li>
                            <li>
                                die Verbreitung und/oder öffentliche Wiedergabe von auf dem Portal verfügbaren Inhalten, soweit Ihnen dies nicht ausdrücklich vom jeweiligen Urheber gestattet oder als Funktionalität auf dem Portal ausdrücklich zur Verfügung gestellt wird.
                            </li>
                        </ol>
                    </li>
                    <li>
                        Ebenfalls <strong>untersagt</strong> ist Ihnen jede Handlung, die geeignet ist, den reibungslosen Betrieb des Portals zu beeinträchtigen, insbesondere die Systeme des Diensteanbieters übermäßig zu belasten.
                    </li>
                    <li>
                        Sollte Ihnen eine illegale, missbräuchliche, vertragswidrige oder sonstwie unberechtigte Nutzung des Portals bekannt werden, so wenden Sie sich bitte an Brunnenstraße 147, 10115 Berlin. Der Diensteanbieter wird den Vorgang dann prüfen und ggf. angemessene Schritte einleiten.
                    </li>
                    <li>
                        Bei Vorliegen eines Verdachts auf rechtswidrige bzw. strafbare Handlungen ist der Diensteanbieter berechtigt und ggf. auch verpflichtet, Ihre Aktivitäten zu überprüfen und ggf. geeignete rechtliche Schritte einzuleiten. Hierzu kann auch die Zuleitung eines Sachverhalts an die Staatsanwaltschaft gehören.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p16">§16 Sperrung von Zugängen</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Der Diensteanbieter kann Ihren Zugang zu dem Portal vorübergehend oder dauerhaft sperren, wenn konkrete Anhaltspunkte vorliegen, dass Sie gegen diese Nutzungsbedingungen und/oder geltendes Recht verstoßen bzw. verstoßen haben, oder wenn der Diensteanbieter ein sonstiges berechtigtes Interesse an der Sperrung hat. Bei der Entscheidung über eine Sperrung wird der Diensteanbieter Ihre berechtigten Interessen angemessen berücksichtigen.
                    </li>
                    <li>
                        Im Falle der vorübergehenden bzw. dauerhaften Sperrung sperrt der Diensteanbieter Ihre Zugangsberechtigung und benachrichtigt Sie hierüber per E-Mail.
                    </li>
                    <li>
                        Im Falle einer vorübergehenden Sperrung reaktiviert der Diensteanbieter nach Ablauf der Sperrzeit die Zugangsberechtigung und benachrichtigt Sie hierüber per E-Mail. Eine dauerhaft gesperrte Zugangsberechtigung kann nicht wiederhergestellt werden. Dauerhaft gesperrte Personen sind von der Teilnahme an dem Portal dauerhaft ausgeschlossen und dürfen sich nicht erneut auf dem Portal anmelden.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>V. Verarbeitung Ihrer personenbezogenen Daten</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p17">§17 Datenschutz</h3>
            </div>
        </div>
                <!--	<ol>
                        <li>
                            Zu den Qualitätsansprüchen des Diensteanbieters gehört es, verantwortungsbewusst mit den persönlichen Daten der Teilnehmer (diese Daten werden nachfolgend „<strong>personenbezogene Daten</strong>“ genannt) umzugehen. Die sich aus Ihrer Anmeldung auf dem Portal sowie aus der Nutzung der verfügbaren Dienste ergebenden personenbezogenen Daten werden vom Diensteanbieter daher nur erhoben, gespeichert und verarbeitet, soweit dies zur vertragsgemäßen Leistungserbringung erforderlich und durch gesetzliche Vorschriften erlaubt, oder vom Gesetzgeber angeordnet ist. Der Diensteanbieter wird Ihre personenbezogenen Daten vertraulich sowie entsprechend den Bestimmungen des geltenden Datenschutzrechts behandeln und nicht an Dritte weitergeben.
                        </li>
                        <li>
                            Hierüber hinaus verwendet der Diensteanbieter Ihre personenbezogenen Daten nur, soweit Sie hierzu ausdrücklich eingewilligt haben. Eine von Ihnen erteilte Einwilligung können Sie jederzeit widerrufen.
                        </li>
                    </ol>-->

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-4 pt-4">
                    Hinweise und Regelungen zum Datenschutz entnehmen Sie bitte unserer <a href="{{ route('page.privacy') }}">Datenschutzerklärung</a>.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>VI. Haftungsbeschränkung</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p18">§18 Haftungsbeschränkung für unentgeltliche Dienste</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-4 pt-4">
                    Sollten Ihnen durch die Nutzung von auf dem Portal unentgeltlich zur Verfügung gestellten Diensten (einschließlich des Abrufs von kostenlosen Inhalten) ein Schaden entstehen, so haftet der Diensteanbieter nur, soweit Ihr Schaden aufgrund der vertragsgemäßen Nutzung der unentgeltlichen Inhalte und/oder Dienste entstanden ist, und nur bei Vorsatz (einschließlich Arglist) und grober Fahrlässigkeit des Diensteanbieters.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p19">§19 Haftungsbeschränkung für kostenpflichtige Dienste</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-4 pt-4">
                    Im Rahmen der Nutzung kostenpflichtiger Dienste (einschließlich des Abrufs von kostenpflichtigen Inhalten) durch Sie haftet der Diensteanbieter nach Maßgabe der nachfolgenden Regelungen:
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Für Schäden, die durch den Diensteanbieter oder durch dessen gesetzlichen Vertreter, leitende Angestellte oder einfache Erfüllungsgehilfen vorsätzlich oder grob fahrlässig verursacht wurde, haftet der Diensteanbieter unbeschränkt.
                    </li>
                    <li>
                        In Fällen der leicht fahrlässigen Verletzung von nur unwesentlichen Vertragspflichten haftet der Diensteanbieter nicht. Im Übrigen ist die Haftung des Diensteanbieters für leicht fahrlässig verursachte Schäden auf die diejenigen Schäden beschränkt, mit deren Entstehung im Rahmen des jeweiligen Vertragsverhältnisses typischerweise gerechnet werden muss (vertragstypisch vorhersehbare Schäden). Dies gilt auch bei leicht fahrlässigen Pflichtverletzungen der gesetzlichen Vertreter, leitenden Angestellten bzw. einfachen Erfüllungsgehilfen des Diensteanbieters.
                    </li>
                    <li>
                        Die vorstehende Haftungsbeschränkung gilt nicht im Falle von Arglist, im Falle von Körper- bzw. Personenschäden, für die Verletzung von Garantien sowie für Ansprüche aus Produkthaftung.
                    </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>VII. Sonstige Bestimmungen</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h3 id="p20">§20 Schriftformerfordernis</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <ol>
                    <li>
                        Sofern in diesen Nutzungsbedingungen nicht ausdrücklich etwas Anderes angegeben ist, sind sämtliche Erklärungen, die im Rahmen der Teilnahme an dem Portal abgegeben werden, in Schriftform oder per E-Mail abzugeben. Die E-Mail-Adresse des Diensteanbieters lautet fabio.bacigalupo@{conf[site][tld]}. Die postalische Anschrift des Diensteanbieters lautet Brunnenstraße 147, 10115 Berlin. Änderungen der Kontaktdaten bleiben vorbehalten. Im Fall einer solchen Änderung wird der Diensteanbieter Sie hierüber in Kenntnis setzen.
                    </li>
                </ol>
            </div>
        </div>
    </section>

@endsection
