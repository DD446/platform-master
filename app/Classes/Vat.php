<?php
/**
 * Work with VAT ids
 * User: fabio
 * Date: 08.12.14
 * Time: 15:26
 */
namespace App\Classes;

use Illuminate\Support\Str;

class Vat {

    private $aSpoken = [
        'BE' => 'Belgien',
        'BG' => 'Bulgarien',
        'CZ' => 'Tschechische Republik',
        'DK' => 'Dänemark',
        "DE" => 'Deutschland',
        'EE' => 'Estland',
        'EL' => 'Griechenland',
        'ES' => 'Spanien',
        'FR' => 'Frankreich',
        'HR' => 'Kroatien',
        'IE' => 'Irland',
        'IT' => 'Italien',
        'CY' => 'Zypern',
        'LV' => 'Lettland',
        'LT' => 'Litauen',
        'LU' => 'Luxemburg',
        'HU' => 'Ungarn',
        'MT' => 'Malta',
        'NL' => 'Niederlande',
        'AT' => 'Österreich',
        'PL' => 'Polen',
        'PT' => 'Portugal',
        'RO' => 'Rumänien',
        'SI' => 'Slowenien',
        'SK' => 'Slowakei',
        'FI' => 'Finnland',
        'SE' => 'Sweden',
        'UK' => 'Großbritannien', # TODO: Remove because of Brexit
        #
        'CH' => 'Schweiz',
    ];

    public function validate($countryCode, $id)
    {
        $vatValidation = new VatValidation(['debug' => false]);

        if ($countryCode == 'AT' && $id[0] != 'U') {
            $id = "U$id";
        }

        if ($countryCode == 'CH' && $id[0] != 'E') {
            $id = "E$id";
        }

        // Spanien (Kanarische Inseln) nicht im VIES System - Überprüfung hier nicht möglich
        // @see http://www.steuerberaterspanien.com/umsatzsteuer_kanarische_inseln.html
        if ($countryCode == 'EK' && $id[0] != 'B') {
            $id = "B$id";
            return true;
        }

        return $vatValidation->check($countryCode, $id);
    }

    public function validateDebug($countryCode, $id)
    {
        $vatValidation = new VatValidation(['debug' => true]);

        if($vatValidation->check($countryCode, $id)) {
            echo '<h1>valid one!</h1>';
            echo 'denomination: ' . $vatValidation->getDenomination(). '<br/>';
            echo 'name: ' . $vatValidation->getName(). '<br/>';
            echo 'address: ' . $vatValidation->getAddress(). '<br/>';
        } else {
            echo '<h1>Invalid VAT</h1>';
        }
    }

    public function getVat($countryCode, $postCode = null)
    {
        $aVat = [
            "BE" => 21,
            "BG" => 20,
            "CZ" => 21,
            "DK" => 25,
            "DE" => 19,
            "EE" => 20,
            "EL" => 23,
            "ES" => 21,
            "FR" => 20,
            "HR" => 25,
            "IE" => 23,
            "IT" => 22,
            "CY" => 19,
            "LV" => 21,
            "LT" => 21,
            "LU" => 17,
            "HU" => 27,
            "MT" => 18,
            "NL" => 21,
            "AT" => 20,
            "PL" => 23,
            "PT" => 23,
            "RO" => 24,
            "SI" => 22,
            "SK" => 20,
            "FI" => 24,
            "SE" => 25,
            "UK" => 20,
        ];

        if (!array_key_exists($countryCode, $aVat)) {
            throw new \Exception("ERROR: Country code unknown");
        }

        if ($countryCode == 'ES' && $postCode && Str::startsWith($postCode, '35')) {
            return 0;
        }

        return $aVat[$countryCode];
    }

    public function getSpoken($countryCode)
    {
        if (array_key_exists($countryCode, $this->aSpoken)) {
            return $this->aSpoken[$countryCode];
        }
        return $countryCode;
    }

    public function getCountries() {
        return $this->aSpoken;
    }

    public function getEuCountries($excludeGermany = false)
    {
        $aCountries = $this->aSpoken;
        if ($excludeGermany) {
            unset($aCountries['DE']);
        }
        unset($aCountries['CH']);

        return $aCountries;
    }

    /**
     * @param integer $country Country name
     * @param integer $vat_number VAT number to test e.g. GB123 4567 89
     * @return integer -1 if country not included OR 1 if the VAT Num matches for the country OR 0 if no match
     */
    public function checkVatNumber( $country, $vat_number )
    {
        switch($country) {
            case 'Austria':
                $regex = '/^(AT){0,1}U[0-9]{8}$/i';
                break;
            case 'Belgium':
                $regex = '/^(BE){0,1}[0]{0,1}[0-9]{9}$/i';
                break;
            case 'Bulgaria':
                $regex = '/^(BG){0,1}[0-9]{9,10}$/i';
                break;
            case 'Cyprus':
                $regex = '/^(CY){0,1}[0-9]{8}[A-Z]$/i';
                break;
            case 'Czech Republic':
                $regex = '/^(CZ){0,1}[0-9]{8,10}$/i';
                break;
            case 'Denmark':
                $regex = '/^(DK){0,1}([0-9]{2}[\ ]{0,1}){3}[0-9]{2}$/i';
                break;
            case 'Estonia':
            case 'Germany':
            case 'Greece':
            case 'Portugal':
                $regex = '/^(EE|EL|DE|PT){0,1}[0-9]{9}$/i';
                break;
            case 'France':
                $regex = '/^(FR){0,1}[0-9A-Z]{2}[\ ]{0,1}[0-9]{9}$/i';
                break;
            case 'Finland':
            case 'Hungary':
            case 'Luxembourg':
            case 'Malta':
            case 'Slovenia':
                $regex = '/^(FI|HU|LU|MT|SI){0,1}[0-9]{8}$/i';
                break;
            case 'Ireland':
                $regex = '/^(IE){0,1}[0-9][0-9A-Z\+\*][0-9]{5}[A-Z]$/i';
                break;
            case 'Italy':
            case 'Latvia':
                $regex = '/^(IT|LV){0,1}[0-9]{11}$/i';
                break;
            case 'Lithuania':
                $regex = '/^(LT){0,1}([0-9]{9}|[0-9]{12})$/i';
                break;
            case 'Netherlands':
                $regex = '/^(NL){0,1}[0-9]{9}B[0-9]{2}$/i';
                break;
            case 'Poland':
            case 'Slovakia':
                $regex = '/^(PL|SK){0,1}[0-9]{10}$/i';
                break;
            case 'Romania':
                $regex = '/^(RO){0,1}[0-9]{2,10}$/i';
                break;
            case 'Sweden':
                $regex = '/^(SE){0,1}[0-9]{12}$/i';
                break;
            case 'Spain':
                $regex = '/^(ES){0,1}([0-9A-Z][0-9]{7}[A-Z])|([A-Z][0-9]{7}[0-9A-Z])$/i';
                break;
            case 'United Kingdom':
                $regex = '/^(GB){0,1}([1-9][0-9]{2}[\ ]{0,1}[0-9]{4}[\ ]{0,1}[0-9]{2})|([1-9][0-9]{2}[\ ]{0,1}[0-9]{4}[\ ]{0,1}[0-9]{2}[\ ]{0,1}[0-9]{3})|((GD|HA)[0-9]{3})$/i';
                break;
            default:
                return -1;
                break;
        }

        return preg_match($regex, $vat_number);
    }
}

/*Belgien BE - 6 / 12 21 12
Bulgarien BG - 9 20 -
Tschechische Republik CZ - 15 21 -
Dänemark DK - - 25 -
Deutschland DE - 7 19 -
Estland EE - 9 20 -
Griechenland EL - 6,5 / 13 23 -
Spanien ES 4 10 21 -
Frankreich FR 2,1 5,5 / 10 20 -
Kroatien HR - 5 / 13 25 -
Irland IE 4,8
9 / 13,5 23 13,5
Italien IT 4 10 22 -
Zypern CY - 5 / 9 19 -
Lettland LV - 12 21 -
Litauen LT - 5 / 9 21 -
Luxemburg LU 3 6 / 12 15 12
Ungarn HU - 5 / 18 27 -
Malta MT - 5 / 7 18 -
Niederlande NL - 6 21 -
Österreich AT - 10 20 12
Polen PL - 5 / 8 23 -
Portugal PT - 6 / 13 23 13
Rumänien RO - 5 / 9 24 -
Slowenien SI - 9,5 22 -
Slowakische Republik SK - 10 20 -
Finnland FI - 10 /14 24 -
Schweden SE - 6 / 12 25 -
Vereinigtes Königreich UK - 5 20 - */
