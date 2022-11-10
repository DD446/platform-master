<?php
/**
 * User: fabio
 * Date: 24.03.20
 * Time: 12:46
 */

namespace App\Classes;


class WebserverLegacy extends LegacyBase
{
    /**
     * WebserverLegacy constructor.
     * @deprecated
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->rootDir . '/lib/podcaster/Domain.php';
    }

    public function write(string $username, string $feedId)
    {
        $oDomain = new \de\App\Domain();
        $aDomain = $oDomain->get($feedId, $username);

        return $oDomain->writeConfig($username, $aDomain[\de\App\Domain::MONGO_FIELD_DOMAIN_HOSTNAME]);
    }
}
