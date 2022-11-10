<?php
/**
 * User: Fabio Bacigalupo <f.bacigalupo@open-haus.de>
 * Date: 07.03.17
 * Time: 11:52
 */
return [
    'title' => 'Kontakt',
    'page_description' => 'Über unser Kontaktformular können Sie Kontakt mit uns aufnehmen.',
    'header' => 'Kontakt',
    'side_header' => 'Support-Anfrage?',
    'subheader' => 'Wir freuen uns über Deine Anfrage. Wir versuchen, Dir eine Antwort innerhalb von 2 Werktagen zu geben.',
    'name' => 'Name',
    'label_name' => 'Name',
    'placeholder_name' => 'Namen eingeben',
    'email' => 'E-Mail',
    'label_email' => 'E-Mail',
    'placeholder_email' => 'E-Mail-Adresse eingeben',
    'comment' => 'Nachricht',
    'label_comment' => 'Nachricht',
    'placeholder_comment' => 'Nachricht eingeben',
    'enquiry_type' => 'Art der Anfrage',
    'label_enquiry_type' => 'Art der Anfrage',
    'send' => 'Anfrage abschicken',
    'enquiry' => [
        'general' => 'Allgemeine Anfrage',
        'support' => 'Support-Anfrage',
        'bill' => 'Frage zu Rechnungen',
        'feedback' => 'Feedback zur Seite',
        'bug' => 'Fehlermeldung',
        'commercial' => 'Kommerzielle Anfrage',
        'feature' => 'Feature-Anfrage',
        'callback' => 'Rückruf',
        'interview' => 'Interview',
        'jobs' => 'Bewerbung',
    ],
    'store_error' => 'Die Anfrage konnte nicht gespeichert werden. Bitte versuchen Sie es erneut!',
    'store_success' => 'Vielen Dank für deine Anfrage! Wir bemühen uns, alle Anfragen innerhalb von 2 Werktagen zu beantworten.',
    'store_success_extra' => 'Wir melden uns bald bei Dir.',
    'mail_subject' => '[podcaster] Kontaktanfrage (:type) von :name',
    'side_header_address' => 'Adresse',
    'side_header_phone' => 'Telefon',
    'side_header_email' => 'E-Mail',
    'all_fields_required' => '* Pflichtfeld',
    'help_text' => 'Bei Problemen wirf bitte zuerst einen Blick in die <a href="' . route('faq.index') . '">Liste der häufigen Fragen und Antworten (FAQ)</a>, recherchiere und diskutiere <a href="https://podster.de/c/podcast-hosting/6" target="_blank">in unserem Forum auf podster</a> oder <a href="' . route('lp.videos') . '">schau Dir unsere Videos</a> an.',
    'legal' => 'Mit dem Absenden der Kontaktanfrage erklärst du Dich mit der Verarbeitung Deiner angegebenen Daten zum Zweck der Bearbeitung Deiner Anfrage einverstanden (<a href="' . route('page.privacy') . '">Datenschutzerklärung und Widerrufshinweise</a>).',
    'placeholder_captcha' => 'Bitte gib hier den Code aus dem Bild ein.',
    'placeholder_mathcaptcha' => 'Gib hier das Ergebnis der Rechenaufgabe ein.',
    'greeting' => 'Hallo :name!',
    'label_math_equation' => 'Löse die Rechenaufgabe: :equation',
    'text_option_enquiry_type_default' => 'Bitte wähle eine Option aus.',
    'text_legal' => 'Mit dem Absenden der Kontaktanfrage erklärst du Dich mit der Verarbeitung Deiner angegebenen Daten zum Zweck der Bearbeitung Deiner Anfrage einverstanden (<a href=":terms">Datenschutzerklärung und Widerrufshinweise</a>).',
    'text_button_send_message' => 'Anfrage abschicken',
];