<?php
return [
    'error_cannot_use_local_domain' => 'Du kannst keine Domain des Dienstes als eigene Domain verwenden.',
    'error_domain_is_not_available' => 'Die Domain ist nicht verfügbar.',
    'error_missing_type_for_hostname' => 'Bei der DNS-Abfrage für diese Domain wurde Typ `CNAME` mit Wert `' . config('app.cname') . '` erwartet. Kein Typ erhalten. <a href="https://www.podcaster.de/faq/antwort-43-wie-verwende-ich-eine-eigene-domain-custom-domain-mit-dem-podcast-hoster" target="_blank">Mehr zur Einrichtung erfährst Du hier.</a>',
    'error_wrong_type_for_hostname' => 'Bei der DNS-Abfrage für diese Domain wurde Typ `CNAME` mit Wert `' . config('app.cname') . '` erwartet, Typ ":type" erhalten. <a href="https://www.podcaster.de/faq/antwort-43-wie-verwende-ich-eine-eigene-domain-custom-domain-mit-dem-podcast-hoster" target="_blank">Mehr zur Einrichtung erfährst Du hier.</a>',
    'error_cname_not_set' => 'CNAME ist nicht vorhanden.  <a href="https://www.podcaster.de/faq/antwort-43-wie-verwende-ich-eine-eigene-domain-custom-domain-mit-dem-podcast-hoster" target="_blank">Mehr zur Einrichtung erfährst Du hier.</a>',
    'error_cname_not_set_correctly' => 'Bei der DNS-Abfrage für diese Domain wurde CNAME ":cname" erwartet, CNAME ":target" erhalten.  <a href="https://www.podcaster.de/faq/antwort-43-wie-verwende-ich-eine-eigene-domain-custom-domain-mit-dem-podcast-hoster" target="_blank">Mehr zur Einrichtung erfährst Du hier.</a>',
    'error_url_invalid' => 'Die URL ist ungültig. Für Umlaute benutze bitte Punycode.',
    'error_url_is_not_available' => 'Die Domain ist nicht verfügbar. Bitte wähle eine andere Domain.',
    'error_domain_must_have_tld' => 'Die Domain muss eine TLD (Endung wie .de) haben.',
    'error_domain_subdomain_is_too_short' => 'Die Subdomain ist zu kurz. Stufe dein Paket hoch, um kürzere Subdomains zu nutzen.',
    'error_domain_is_taken' => 'Die Domain ist leider bereits andererseits vergeben und daher nicht verfügbar.',
    'error_url_may_not_contain_point' => 'Die Subdomain darf keinen Punkt enthalten.',
    'label_checkbox_ignore_cname_errors' => 'CNAME Einrichtungsfehler ignorieren ',
];
