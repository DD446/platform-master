<?php
/**
 * User: fabio
 * Date: 02.04.20
 * Time: 22:19
 */

namespace App\Classes;


use Illuminate\Support\Str;

class Domain
{
    const SUBDOMAIN_LENGTH_MINIMUM = 3;
    const SUBDOMAIN_PREMIUM_LENGTH_MINIMUM = 2;
    const IS_CUSTOM = 1;

    /**
     * @param  string  $username
     * @return array
     */
    public function getDomainDefaults(string $username): array
    {
        $username = Str::lower($username);
        $aLocalDomains = $this->getLocalDomains();
        $defaultDomain = array_shift($aLocalDomains);
        $tld = substr($defaultDomain, strrpos($defaultDomain, '.') + 1);
        $subdomain = substr($defaultDomain, 0, strlen($defaultDomain) - strlen($tld)-1);

        return [
            "tld" => $tld,
            "subdomain" => "{$username}.{$subdomain}",
            "protocol" => "https",
            "hostname" => "https://{$username}.{$subdomain}.{$tld}",
            "domain" => $defaultDomain,
            "is_custom" => false,
            'website_type' => false,
            'website_redirect' => false,
            'feed_redirect' => false,
        ];
    }

    public function getLocalDomains()
    {
        $aLocalDomains = config('app.local_domains');

        if (count($aLocalDomains) < 1) {
            $aLocalDomains = [config('app.domain')];
        }
        return $aLocalDomains;
    }
}
