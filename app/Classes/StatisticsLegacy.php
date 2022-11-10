<?php
/**
 * User: fabio
 * Date: 30.08.19
 * Time: 12:08
 */

namespace App\Classes;


class StatisticsLegacy extends LegacyBase
{
    /**
     * StatisticsLegacy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->rootDir . '/modules/podcaster/classes/FeedStatisticsDAO.php';
    }

    public function getSubscriberCount($username, array $dateRange, $feedId = null)
    {
        return \FeedStatisticsDAO::singleton()->getSubscriberCount($username, $dateRange, $feedId);
    }
}
