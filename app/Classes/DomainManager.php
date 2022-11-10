<?php
/**
 * User: fabio
 * Date: 13.02.14
 * Time: 22:01
 */

namespace App\Classes;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exceptions\DomainException;
use App\Exceptions\ServerConfigWriteException;
use App\Jobs\ReloadWebserver;
use App\Models\Feed;
use App\Models\User;
use App\Models\UserForbidden;

/**
 * Class Domain
 * @package de\podcaster
 * https://data.iana.org/TLD/tlds-alpha-by-domain.txt
 */
class DomainManager {

    const MIN_LENGTH_SUBDOMAIN = 3;
    const MIN_LENGTH_SUBDOMAIN_PREMIUM = 2;

    /**
     * @param  string  $username
     * @param  string  $feedId
     * @return bool
     * @throws ServerConfigWriteException
     */
    public function writeConfig(string $username, string $feedId): bool
    {
        $feed = Feed::where('username', '=', new \MongoDB\BSON\Regex('^' . preg_quote($username) . '$', 'i'))
            ->where('feed_id', '=', $feedId)
            ->select(['username', 'domain', 'settings'])
            ->firstOrFail();
        $nc = new NginxServerConfigWriter($username, "{$feed->domain['subdomain']}.{$feed->domain['tld']}");

        // Find redirects (website/feed)
        $feeds = Feed::where('username', '=', new \MongoDB\BSON\Regex('^' . preg_quote($username) . '$', 'i'))
            ->select(['feed_id', 'domain'])
            ->get();

        foreach($feeds as $aVal) {
            if (isset($aVal->domain['feed_redirect'])
                && $aVal->domain['feed_redirect']) {
                $nc->setRedirectUrlFeed($aVal->feed_id, $aVal->domain['feed_redirect']);
            }
        }

        if (isset($feed->domain['website_redirect'])
            && $feed->domain['website_redirect']) {
            $nc->setRedirectUrlBlog($feed->domain['website_redirect']);
            // Redirect overwrites default website type
            $nc->setWebsiteType('website_redirect');
        } elseif (isset($feed->domain['website_type'])
            && $feed->domain['website_type']) {
            $nc->setWebsiteType($feed->domain['website_type']);
        }

        if (isset($feed->settings['uses_protection'])
            && $feed->settings['uses_protection'] === "yes") {
            $nc->setUsesProtection(true);
        }

        //$nc->setLimitRate(NginxServerConfigWriter::SPEED_HD);
        $nc->create();

        if (!$nc->exists()) {
            Log::error("ERROR: User '$username': Failed to create server config for domain '{$feed->domain['subdomain']}.{$feed->domain['tld']}'.");
            throw new ServerConfigWriteException("ERROR: Could not write http server config");
        }

        ReloadWebserver::dispatch($feedId)->onConnection('redis')->onQueue('superuser');

        return true;
    }

    /**
     * @param  string  $hostname
     * @return bool
     * @throws DomainException
     */
    public function checkCname(string $hostname): bool
    {
        $dns = dns_get_record($hostname);
        $aInfo = array_shift($dns);

        if (is_null($aInfo) || !isset($aInfo['type'])) {
            throw new DomainException(trans('domain.error_missing_type_for_hostname'));
        }

        if (!isset($aInfo['type']) || strtoupper($aInfo['type']) != 'CNAME') {
            throw new DomainException(trans('domain.error_wrong_type_for_hostname', ['type' => $aInfo['type']]));
        }

        if (!isset($aInfo['target'])) {
            throw new DomainException(trans('domain.error_cname_not_set'));
        }

        $cname = config('app.cname');

        if ($aInfo['target'] != $cname) {
            throw new DomainException(trans('domain.error_cname_not_set_correctly', ['cname' => $cname, 'target' => $aInfo['target']]));
        }

        return true;
    }

    public function isLocal(string $domain)
    {
        return in_array($domain, $this->getLocal());
    }

    /**
     * @param  string  $domain
     * @param  string  $tld
     * @param  string  $protocol
     * @return bool
     * @throws DomainException
     */
    public function isValidCustomDomain(string $domain, string $tld): bool
    {
        // Attention: This only works for local domains with one point, e.g. podcaster.de
        // but not for domains like podcaster.co.uk
        // TODO: I18N
        // This checks possible hacks. An user must not use a local domain as a custom domain,
        // e.g. prevent things like www.podcaster.de as custom domains
        // $domain can be something like www.podcaster and $tld any valid ICAN TLD, e.g. .de
        // So here we cut www.podcaster into podcaster and attach the tld to check 'podcaster.de'
        // which would turn out to be a local domain and which is not allowed here
        if ($this->isLocal(Str::afterLast($domain, '.') . '.' . $tld)) {
            throw new DomainException(trans('domain.error_cannot_use_local_domain'));
        }
        $this->checkCname($domain . '.' . $tld);

        return true;
    }

    /**
     * This check is only for custom and default local domains not custom (external) domains
     *
     * @param  User  $user
     * @param  string  $domain
     * @param  string  $tld
     * @param  string  $protocol
     * @return bool
     * @throws DomainException
     */
    public function isValidLocalDomain(User $user, string $domain, string $tld): bool
    {
        // We do not use anything else locally anymore
        $protocol = 'https';
        $username = $user->username;
        $hostname = $this->getHostname($domain, $tld, $protocol);

        if (empty($tld)) {
            throw new DomainException('ERROR: Domain cannot be empty');
        }

        if (filter_var("{$protocol}://{$domain}.{$tld}", FILTER_VALIDATE_URL) === false) {
            throw new DomainException(trans('domain.error_url_invalid'));
        }

        if ($domain != $username && UserForbidden::whereUsername($domain)->count() > 0) {
            throw new DomainException(trans('domain.error_url_is_not_available'));
        }

        // OK: User 'jam' exists jam.podcaster.de
        if ($domain != $username) {
            if ($this->isUsername($domain)) {
                throw new DomainException(trans('domain.error_url_is_not_available'));
            }
        }

        // $tld is a local domain here, e.g. podcaster.de
        // not a TLD in classic means
        if (!$this->isLocal($tld)) {
            throw new DomainException(trans('domain.error_url_is_not_available'));
        }

        if (Str::contains($domain, '.')) {
            throw new DomainException(trans('domain.error_url_may_not_contain_point'));
        }

        if (strpos($hostname, '.') === false) {
            throw new DomainException(trans('domain.error_url_is_not_available'));
        }

        $minLengthSubdomain = self::MIN_LENGTH_SUBDOMAIN;
        $aFeaturePremiumSubdomains = get_package_feature_premium_subdomains($user->package, $user);

        if ($aFeaturePremiumSubdomains['total'] > 0) {
            $minLengthSubdomain = self::MIN_LENGTH_SUBDOMAIN_PREMIUM;
        }

        if (strlen($domain) === 1) {
            throw new DomainException(trans('domain.error_url_is_not_available'));
        }

        if (strlen($domain) < $minLengthSubdomain) {
            throw new DomainException(trans('domain.error_domain_subdomain_is_too_short'));
        }

        if (!$this->isAvailable($username, $domain, $tld)) {
            throw new DomainException(trans('domain.error_domain_is_taken'));
        }

        return true;
    }

    /**
     * @param  string  $domain
     * @param  string  $tld
     * @param  string  $protocol
     * @return string
     */
    private function getHostname(string $domain, string $tld, string $protocol = 'https'): string
    {
        return $protocol . '://' . $domain . '.' . $tld;
    }

    /**
     * @param  string  $username
     * @param  string  $domain
     * @param  string  $tld
     * @return bool
     */
    public function isAvailable(string $username, string $domain, string $tld)
    {
        Log::debug("Username '$username': Checking domain '$domain' for TLD '$tld'.");

        $count = Feed::where('username', '!=', $username)
            ->where(function ($query) use ($domain, $tld) {
                return $query->where('domain.hostname', $this->getHostname($domain, $tld))
                    ->orWhere('domain.hostname', $this->getHostname($domain, $tld, 'http'));
            })
            ->count();

        return $count === 0;
    }

    public function getLocal()
    {
        $localDomains = config('app.local_domains');

        if (count($localDomains) < 1) {
            $localDomains = [config('app.domain')];
        }
        return $localDomains;
    }


    private function isUsername($domain)
    {
        if (preg_match('/[^a-z0-9]/i', $domain)) {
            return false;
        }

        return User::whereUsername($domain)->count() > 0;
    }
}
