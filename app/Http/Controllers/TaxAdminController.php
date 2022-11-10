<?php

namespace App\Http\Controllers;

use App\Models\UserPayment;
use Illuminate\Http\Request;

class TaxAdminController extends Controller
{
    public function export()
    {
        ini_set('max_execution_time', 0);

        $year = \request('year', date('Y'));
        $content = UserPayment::with(['receiver' => function($query) { return $query->withTrashed(); }, 'receiver.userbillingcontact'])
            ->refundable()
            ->whereYear('date_created', '=', $year)
            ->take(100)
            ->get();

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($content) {
                $csvExporter = new \Laracsv\Export();
                $csvExporter->beforeEach(function ($payment) {
                    // Now notes field will have this value
                    $payment->amount_currency = $payment->amount . ' ' . $payment->currency;
                    $payment->date = $payment->date_created->format('d.m.Y');
                    $payment->name =  isset($payment->receiver->userbillingcontact->last_name) && !empty($payment->receiver->userbillingcontact->last_name) ? trim(ucfirst($payment->receiver->userbillingcontact->first_name) . ' ' . ucfirst($payment->receiver->userbillingcontact->last_name)) :  trim(ucfirst($payment->receiver->first_name) . ' ' . ucfirst($payment->receiver->last_name));
                    $payment->street =  isset($payment->receiver->userbillingcontact->street) && !empty($payment->receiver->userbillingcontact->street) ? trim(ucfirst($payment->receiver->userbillingcontact->street) . ' ' . $payment->receiver->userbillingcontact->housenumber) :  trim($payment->receiver->street . ' ' . ucfirst($payment->receiver->housenumber));
                    $payment->city =  isset($payment->receiver->userbillingcontact->city) && !empty($payment->receiver->userbillingcontact->city) ? trim($payment->receiver->userbillingcontact->post_code . ' ' . ucfirst($payment->receiver->userbillingcontact->city)) :  trim($payment->receiver->post_code . ' ' . ucfirst($payment->receiver->city));
                    $payment->country_code =  isset($payment->receiver->userbillingcontact->country) && !empty($payment->receiver->userbillingcontact->country) ? $payment->receiver->userbillingcontact->country :  $payment->receiver->country;
                    $payment->paid =  $payment->is_paid === 1 ? 'Ja' : 'Nein';
                });
                $filehandler = fopen('php://output', 'r');
                fwrite($filehandler,
                    //$csvExporter->build($content, ['date_created', 'bill_id', 'receiver.userbillingcontact.first_name' => 'Vorname Rechnungskontakt', 'receiver.userbillingcontact.last_name' => 'Nachname Rechnungskontakt'])->getWriter()->getContent());
                    $csvExporter->build($content, [
                        'date_created' => 'Datum (formatiert)',
                        'date' => 'Datum',
                        'bill_id' => 'Rech.-Nr.',
                        'amount_currency' => 'Betrag',
                        'amount' => 'Summe (Brutto)',
                        'paid' => 'Bezahlt',
                        'receiver.usr_id' => 'Kunden-ID',
                        'name' => 'Name',
                        'street' => 'Straße und Hausnummer',
                        'city' => 'PLZ und Stadt',
                        'country_code' => 'Land',
                        'receiver.userbillingcontact.vat_id' => 'UStID',
                        'receiver.userbillingcontact.first_name' => 'Vorname Rechnungskontakt',
                        'receiver.userbillingcontact.last_name' => 'Nachname Rechnungskontakt',
                        'receiver.userbillingcontact.street' => 'Straße Rechnungskontakt',
                        'receiver.userbillingcontact.housenumber' => 'Hausnummer Rechnungskontakt',
                        'receiver.userbillingcontact.city' => 'Stadt Rechnungskontakt',
                        'receiver.userbillingcontact.post_code' => 'PLZ Rechnungskontakt',
                        'receiver.userbillingcontact.country' => 'Land Rechnungskontakt',
                        'receiver.userbillingcontact.organisation' => 'Organisation Rechnungskontakt',
                        'receiver.userbillingcontact.department' => 'Abteilung Rechnungskontakt',
                        'receiver.first_name' => 'Vorname Kunde',
                        'receiver.last_name' => 'Nachname Kunde',
                        'receiver.street' => 'Straße Kunde',
                        'receiver.housenumber' => 'Hausnummer Kunde',
                        'receiver.city' => 'city Kunde',
                        'receiver.post_code' => 'PLZ Kunde',
                        'receiver.country' => 'Land Kunde',
                    ])->getWriter()->getContent());
                fclose($filehandler);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-disposition' => 'attachment; filename="podcaster_bills_year_' . $year . '.csv"',
            ]);
    }

    public function agenda()
    {
        ini_set('max_execution_time', 0);

        $year = \request('year', date('Y'));
        $content = UserPayment::with(['receiver' => function($query) { return $query->withTrashed(); }, 'receiver.userbillingcontact'])
            ->refundable()
            ->where('is_paid', '=', UserPayment::IS_PAID)
            ->whereYear('date_created', '=', $year)
            ->get();

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($content) {
                $csvExporter = new \Laracsv\Export();
                $csvExporter->beforeEach(function ($payment) {
                    $payment->contra_account = 8400;
                    $countryCode =  isset($payment->receiver->userbillingcontact->country) && !empty($payment->receiver->userbillingcontact->country) ? $payment->receiver->userbillingcontact->country :  $payment->receiver->country;
                    $vatId = '';

                    if ($countryCode != 'DE' && $countryCode != '') {
                        if ($countryCode == 'GB') {
                            $countryCode = 'UK';
                        }
                        $value = $payment->receiver->userbillingcontact->vat_id;
                        if ($countryCode == 'AT' && $value && starts_with($value, 'U')) {
                            $value = 'AT' . $value;
                        }
                        if ((new \Ddeboer\Vatin\Validator())->isValid($value)) {

                            try {
                                (new \App\Classes\Vat)->getVat($countryCode);
                                $payment->contra_account = 8336;
                                $vatId = $value;
                            } catch (\Exception $e) {
                                $payment->contra_account = 8338;
                            }
                        } else {
                            $payment->contra_account = 8338;
                        }
                    }
                    // Now notes field will have this value
                    $payment->date = $payment->date_created->format('d.m.Y');
                    $payment->country_code =  isset($payment->receiver->userbillingcontact->country) && !empty($payment->receiver->userbillingcontact->country) ? $payment->receiver->userbillingcontact->country :  $payment->receiver->country;
                    $payment->tax_key = '';
                    $payment->beleg2 = '';
                    $payment->kost1 = '';
                    $payment->kost2 = '';
                    $payment->skonto = '';
                    $payment->account = 1371;
                    $payment->extra_type = '';
                    $payment->extra_info = '';
                    $payment->vat_id = $vatId;

                    if ($countryCode != 'DE' && !$vatId) {
                        $payment->extra_info = $payment->receiver->userbillingcontact->vat_id ?? '';
                        if ($payment->extra_info) {
                            $payment->extra_type = $countryCode;
                        }
                    }
                    //'country_code' => 'Land',
                    //'receiver.userbillingcontact.vat_id' => 'UStID',
                });
                $filehandler = fopen('php://output', 'r');
                fwrite($filehandler,
                    $csvExporter->build($content, [
                        'amount' => 'Umsatz in Euro', # ok
                        'tax_key' => 'Steuerschlüssel', # empty
                        'contra_account' => 'Gegenkonto', # TODO
                        'bill_id' => 'Beleg1', # ok
                        'beleg2' => 'Beleg2', # empty
                        'date' => 'Datum', # ok
                        'account' => 'Konto', # ok
                        'kost1' => 'Kost1', # empty
                        'kost2' => 'Kost2', # empty
                        'skonto' => 'Skonto in Euro', # empty
                        'posting_text' => 'Buchungstext', # empty
                        'vat_id' => 'Umsatzsteuer-ID', # TODO
                        'extra_type' => 'Zusatzart', # empty
                        'extra_info' => 'Zusatzinformation', # empty
                    ])->getWriter()->getContent());
                fclose($filehandler);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-disposition' => 'attachment; filename="podcaster_bills_year_' . $year . '_agenda_export.csv"',
            ]);
    }
}
