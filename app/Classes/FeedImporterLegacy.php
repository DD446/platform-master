<?php
/**
 * User: fabio
 * Date: 02.09.20
 * Time: 20:47
 */

namespace App\Classes;


class FeedImporterLegacy extends LegacyBase
{
    /**
     * FeedWriterLegacy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->rootDir . '/modules/podcaster/classes/FeedDAO.php';
    }

    /**
     * @param  string  $feedId
     * @param  string  $username
     * @param  string  $url
     * @return string
     */
    public function import(string $feedId, string $username, string $url): string
    {
        return \FeedDAO::singleton()->import($feedId, $username, $url);
    }
}
