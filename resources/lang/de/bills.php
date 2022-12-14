<?php
return [
    'page_title_index' => 'Rechnungen zum Ansehen und Herunterladen + Rechnungsanschrift',
    'header_bills' => 'Rechnungen',
    'subheader_bills' => 'Hier kannst Du Deine Rechnungen herunterladen und Deine Rechnungsanschrift anpassen.',
    'header_billing_address' => 'Rechnungsanschrift',
    'title_bill' => 'Rechnung',
    'title_bill_date' => 'Rechnungsdatum',
    'title_bill_amount' => 'Rechnungsbetrag (brutto)',
    'label_first_name' => 'Vorname',
    'label_last_name' => 'Nachname',
    'label_email' => 'E-Mail-Adresse',
    'label_city' => 'Ort',
    'label_postcode' => 'Postleitzahl',
    'label_bill_by_email' => 'Rechnung mit Anhang',
    'hint_bill_by_email' => 'Die Rechnung als PDF-Datei per E-Mail schicken.',
    'label_street' => 'Straße',
    'label_housenumber' => 'Hausnummer',
    'label_country' => 'Land',
    'label_organisation' => 'Organisation (Unternehmen, Firma, Verein, etc.)',
    'label_department' => 'Abteilung',
    'label_vatid' => 'Umsatzsteuer-ID (VAT-ID)',
    'label_vatid_short' => 'USt-Id',
    'label_bill_number' => 'Rechnungsnummer',
    'label_bill_date' => 'Rechnungsdatum',
    'label_bill_due_date' => 'Fälligkeit',
    'label_bill_due_terms' => 'Zahlbar innerhalb von 10 Tagen',
    'label_item' => 'Leistung',
    'label_button_download_bill_as_pdf' => 'PDF herunterladen',
    'label_button_label_bill_as_pdf' => 'PDF',
    'title_download_bill_as_pdf' => 'Rechnung als PDF herunterladen',
    'label_tax_with_vat' => 'USt. (:vat%)',
    'text_billing_item' => 'Einzahlung auf Guthaben-Konto von :service',
    'text_billing_hint_tax' => 'Leistungsdatum ist gleich Rechnungsdatum',
    'label_unpaid' => 'Offen',
    'label_paid' => 'Bezahlt',
    'label_refunded' => 'Storniert',
    'title_refunded_bill' => 'Diese Rechnung wurde storniert.',
    'label_bill_to' => 'Rechnung an',
    'label_amount_sum' => 'Summe',
    'label_amount_net' => 'Netto',
    'label_amount_gross' => 'Brutto',
    'label_bill_signature_disclaimer' => 'Diese Rechnung wurde maschinell erstellt und ist ohne Unterschrift gültig.',
    'text_fabio_vatid' => 'DE222388205',
    'header_bill' => 'Rechnung',
    'label_extras' => 'Zusatzinformationen',
    'hint_extras' => 'Dieser Text wird als Teil der Rechnung angezeigt.',
    'placeholder_first_name' => 'Vorname des Rechnungsempfängers',
    'placeholder_last_name' => 'Nachname des Rechnungsempfängers',
    'placeholder_email' => 'E-Mail-Adresse des Rechnungsempfängers',
    'placeholder_organisation' => 'Organisation',
    'placeholder_department' => 'Abteilung',
    'placeholder_street' => 'Straße',
    'placeholder_housenumber' => 'Hausnummer',
    'placeholder_city' => 'Ortsangabe',
    'placeholder_postcode' => 'Postleitzahl',
    'placeholder_country' => 'Wähle das Land aus',
    'placeholder_vatid' => 'Umsatzsteuer-ID',
    'placeholder_extras' => 'Hier können Sie einen Freitext eingeben, der unten auf der Rechnung angezeigt wird.',
    'choose_a_country' => 'Land auswählen:',
    'legend_name' => 'Ansprechpartner',
    'legend_address' => 'Kontaktadresse',
    'legend_organisation' => 'Organisation',
    'legend_extras' => 'Weiteres',
    'button_text_submit' => 'Rechnungsanschrift speichern',
    'title_tab_bills' => 'Rechnungen',
    'title_tab_billing_contact' => 'Rechnungsanschrift',
    'validation_error_vat_id' => 'Die Umsatzsteuer-ID ist nicht korrekt.',
    'success_saving_billing_contact' => 'Du hast die Rechnungsanschrift erfolgreich gespeichert.',
    'error_saving_billing_contact' => 'Die Kontaktdaten konnten nicht gespeichert werden. Bitte probiere es erneut.',
    'error_validation_saving_billing_contact' => 'Bitte beachte die folgenden Hinweise.',
    'title_error_validation_saving_billing_contact' => 'Es ist ein Fehler aufgetreten!',
    'validation_last_name_required' => 'Der Nachname ist eine Pflichtangabe.',
    'validation_postcode_required' => 'Die Postleitzahl ist eine Pflichtangabe.',
    'validation_city_required' => 'Der Ort ist eine Pflichtangabe.',
    'validation_housenumber_required' => 'Die Hausnummer ist eine Pflichtangabe.',
    'validation_street_required' => 'Die Straße ist eine Pflichtangabe.',
    'validation_country_required' => 'Das Land ist eine Pflichtangabe.',
    'validation_email_invalid' => 'Die Angabe muss eine gültige E-Mail-Adresse sein.',
    'error_file_inaccessible' => 'Die Datei ist nicht abrufbar.',
    'text_no_bills' => 'Da Du bisher kein Guthaben eingezahlt hast, liegt noch keine Rechnung vor.',
    'text_add_funds' => 'Zahle hier Guthaben ein.',
    'billing_company_name' => 'Fabio Bacigalupo',
    'billing_company_address_street' => 'Brunnenstraße 147',
    'billing_company_address_city' => '10115 Berlin',
    'billing_company_address_country' => 'Deutschland',
    'billing_company_brand' => config('app.name'),
    'billing_company_address_email' => 'rechnungen@' . config('app.name'),
    'billing_company_address_web' => 'www.' . config('app.name'),
    'billing_company_phone' => 'Tel.: +49 (0)30 549072654',
    'billing_company_bank_name' => 'Commerzbank AG',
    'billing_company_bank_iban' => 'DE60100800000579632500',
    'billing_company_bank_bic' => 'DRESDEFF100',
    'title_unpaid_bill' => 'Bitte begleiche die Rechnung per Banküberweisung.',
    'text_bill_hint_payable' => 'Bitte überweisen Sie den Betrag ohne Abzug innerhalb von 10 Tagen auf das nachstehend genannte Konto.',
    'text_bill_hint_reverse_charge_header' => 'Steuerschuldnerschaft des Leistungsempfängers – Reverse Charge',
    'text_bill_hint_reverse_charge' => 'Als Leistungsempfänger schulden Sie als Unternehmer die Umsatzsteuer nach § 13b. This bill is without VAT because of „Reverse Charge“.',
    'accounting_activity_refunds' => 'Rückerstattung :details',
    'accounting_activity_booking' => 'Buchung Paket :details',
    'accounting_activity_extras' => 'Zusatzleistung :details',
    'accounting_activity_type' => '{0} Erstattung|{1} Guthaben|{2} Paket|{3} Zusatzoption|{4} Encoding',
    'accounting_activity_add_funds_type' => '{1} Gutschrift auf das Guthaben-Konto|{2} Einzahlung auf das Guthaben-Konto (Banküberweisung)|{3} Einzahlung auf das Guthaben-Konto (Auslandsüberweisung)|{4} Einzahlung auf das Guthaben-Konto (PayPal)|{5} Einzahlung auf das Guthaben-Konto (Rechnung)|{6} Gutschrift auf das Guthaben-Konto (Transfer aus Werbeerlösen)',
    'notification_funds_subject' => 'Guthaben-Konto',
    'notification_funds_salutation' => '{5}Ihr :service Team|Dein :service Team',
    'notification_funds_message' => '{5} Wir haben eine Buchung auf Ihrem Guthaben-Konto vorgenommen. Für ":description" haben wir :amount :currency verrechnet. Ihr Guthaben beträgt jetzt :funds :currency.|Wir haben eine Buchung auf Deinem Guthaben-Konto vorgenommen. Für ":description" haben wir :amount :currency verrechnet. Dein Guthaben beträgt jetzt :funds :currency.',
    'notification_refund_message' => '{5} Wir haben eine Rückerstattung auf Ihr Guthaben-Konto geleistet. Für ":description" haben wir :amount :currency erstattet. Ihr Guthaben beträgt jetzt :funds :currency.|Wir haben eine Rückerstattung auf Dein Guthaben-Konto geleistet. Für ":description" haben wir :amount :currency erstattet. Dein Guthaben beträgt jetzt :funds :currency.',
    'notification_funds_button_text' => 'Buchungen ansehen',
    'notification_funds_greetings' => '{5} Hallo Frau/Herr :last_name!|Hallo :first_name :last_name!',
    'notification_pay_bill' => 'Bitte denken Sie daran, Ihre Rechnung innerhalb von 10 Tagen per Banküberweisung zu begleichen.',
    'notification_add_funds' => '{5} Ihr Guthaben ist negativ. Bitte füllen Sie es auf.|Dein Guthaben ist negativ. Bitte fülle es auf.',
    'notification_button_add_funds' => 'Guthaben aufladen',
    'mail_subject_unpaid_bill_reminder_friendly' => 'Zahlungserinnerung zur beigefügten Rechnung :bill',
    'mail_subject_unpaid_bill_reminder_firm' => 'Zahlungsaufforderung zur beigefügten Rechnung :bill',
    'mail_subject_unpaid_bill_reminder_second_monition' => 'Erste Mahnung zur beigefügten Rechnung :bill',
    'mail_subject_unpaid_bill_reminder_last_monition' => 'Letzte Mahnung zur beigefügten Rechnung :bill',
    'mail_salutation_unpaid_bill_reminder' => 'Sehr geehrte(r) Frau/Herr :name,',
    'mail_salutation_unpaid_bill_reminder_no_name' => 'Sehr geehrte(r) Kundin/Kunde,',
    'mail_intro_unpaid_bill_reminder_friendly' => 'Sie haben sicherlich zwischen all den Podcast-Aufnahmen vergessen, die beigefügte Rechnung rechtzeitig zu begleichen. Bitte überweisen Sie den offenen Betrag zeitnah auf unser Konto.',
    'mail_intro_unpaid_bill_reminder_firm' => 'leider haben wir weiterhin keinen Zahlungseingang für die offene Rechnung verbuchen können.',
    'mail_intro_unpaid_bill_reminder_second_monition' => 'wir erinnern Sie hiermit daran, dass wir Sie bereits 2x aufgefordert haben, Ihre offene Rechnung zu begleichen.',
    'mail_intro_unpaid_bill_reminder_last_monition' => 'da wir trotz aller Zahlungserinnerungen bisher keine Zahlung über Ihre offene Rechnung verbuchen konnten, sehen wir uns gezwungen, Ihr Konto zu sperren.',
    'mail_header_bill_data_unpaid_bill_reminder' => 'Übersicht',
    'mail_table_receiver_unpaid_bill_reminder' => 'Empfänger',
    'mail_table_bank_unpaid_bill_reminder' => 'Bank',
    'mail_table_bic_swift_unpaid_bill_reminder' => 'BIC/Swift',
    'mail_table_iban_unpaid_bill_reminder' => 'IBAN',
    'mail_table_purpose_unpaid_bill_reminder' => 'Verwendungszweck',
    'mail_table_due_date_unpaid_bill_reminder' => 'Fälligskeitsdatum',
    'mail_table_amount_unpaid_bill_reminder' => 'Betrag',
    'mail_hint_bill_data_unpaid_bill_reminder' => 'Für den schnellen Überblick die Daten zur Zahlung:',
    'mail_footer_unpaid_bill_reminder' => 'Ihr :service Team',
    'mail_link_unpaid_bill_reminder' => 'Ihre Rechnungen',
    'mail_hint_outro_unpaid_bill_reminder_friendly' => 'Bitte begleichen Sie den offenen Betrag in den nächsten Tagen.',
    'mail_hint_outro_unpaid_bill_reminder_firm' => 'Überweisen Sie bitte den offenen Betrag innerhalb der nächsten 5 Werktage auf unser Konto.',
    'mail_hint_outro_unpaid_bill_reminder_second_monition' => 'Wir erwarten Ihren Zahlungseingang innerhalb der nächsten 3 Werktage.',
    'mail_hint_outro_unpaid_bill_reminder_last_monition' => 'Die offene Rechnung werden wir an ein Inkasso-Unternehmen abtreten, wenn Sie die Rechnung nicht doch noch innerhalb der nächsten 3 Werktage begleichen.',
    'mail_info_automated_reminder' => 'Bei längeren Zahlungsvorgängen müssen Sie uns nicht gesondert kontaktieren. Diese Zahlungserinnerung wird automatisch verschickt.',
    'account_blocked' => 'Dein Konto ist gesperrt. Bitte prüfe Dein Guthaben und Deine Rechnungen auf Außenstände und begleiche diese.',
    'notification_payment_subject' => 'Rechnung :id (:state)',
    'notification_payment_action' => 'Rechnungsübersicht',
    'notification_payment_text' => 'Auf Ihr Guthaben-Konto wurden :funds eingezahlt. Im Anhang finden Sie die Rechung dafür.',
    'notification_payment_outro' => '{5} Vielen Dank für Ihre Buchung!|Vielen Dank für Deine Buchung!',
    'notification_payment_salutation' => '{5} Ihr ' . config('app.name') . ' Team|Dein ' . config('app.name') . ' Team',
    'notification_payment_pay_bill' => '{5} Bitte denken Sie daran, die Rechnung zeitnah per Banküberweisung zu begleichen. Unsere Bankverbindung finden Sie in der Rechnung.|Bitte denke daran, die Rechnung zeitnah zu begleichen. Unsere Bankverbindung findest Du in der Rechnung.',
    'spoken_state' => '{0} Offen|{1} Bezahlt',
];
