<?php
/**
 * User: fabio
 * Date: 29.12.14
 * Time: 11:23
 */

namespace App\Classes;

class TaxCalculator {

    private $countryCode;
    private $gross;
    private $homeCountry = 'DE';
    private $net;
    private $vat = 0;
    private bool $isReverseCharge = false;
    private $postCode;

    public function __construct($countryCode, $hasVat = null, $postCode = null)
    {
        $oVat = new Vat();

        try {
            $this->setVat($oVat->getVat($countryCode, $postCode));
            $this->setCountryCode($countryCode);
            $this->setPostCode($postCode);

            if ($this->getCountryCode() != $this->homeCountry
                && !is_null($hasVat)
                && strlen($hasVat) > 0) {
                // If a (valid) VAT ID is provided by the customer
                // and the customer is not from Germany (our home country)
                // we do not charge VAT
                $this->setVat(0);
                $this->isReverseCharge = true;
            }
        } catch (\Exception $e) {
            // If no country code is found we have a customer from a non-european country
            // where we do not charge VAT e.g. a customer from Switzerland
            $this->setVat(0);
        }
    }

    /**
     * @return mixed
     */
    public function getVat($forCalculation = false)
    {
        if ($forCalculation === true) {
            $vat = $this->vat;
            // We expect VAT to be higher than 1% which is the case in every country in the EU
            if ($vat > 1) {
                $vat = $vat / 100;
            }
            $vat += 1;

            return $vat;
        }
        return $this->vat;
    }

    /**
     * @param mixed $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return mixed
     */
    public function getGross($formatted = false)
    {
        if (!isset($this->gross)) {
            if (isset($this->net)) {
                $this->gross = $this->net * $this->getVat(true);
            } else {
                throw new \Exception("Unable to get gross. Net is not set.");
            }
        }

        if ($formatted === true) {
            return sprintf("%01.2f", $this->gross);
        }
        return $this->gross;
    }

    /**
     * @return mixed
     */
    public function getNet($formatted = false)
    {
        if (!isset($this->net)) {
            if (isset($this->gross)) {
                $this->net = round($this->gross/(100+$this->getVat())*100, 2);
            } else {
                throw new \Exception("Unable to get net. Gross is not set.");
            }
        }

        if ($formatted === true) {
            return sprintf("%01.2f", $this->net);
        }
        return $this->net;
    }

    /**
     * @param mixed $net
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * @param mixed $gross
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    public function getTax($formatted = false) {
        $tax = $this->getGross() - $this->getNet();

        if ($formatted === true) {
            $tax = sprintf("%01.2f", $tax);
        }
        return $tax;
    }

    private function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    private function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getHomeCountry()
    {
        return $this->homeCountry;
    }

    /**
     * @param string $homeCountry
     */
    public function setHomeCountry($homeCountry)
    {
        $this->homeCountry = $homeCountry;
    }

    public function isReverseCharge(): bool
    {
        return $this->isReverseCharge;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }
}
