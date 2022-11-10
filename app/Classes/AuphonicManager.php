<?php
/**
 * User: fabio
 * Date: 12.05.22
 * Time: 16:19
 */

namespace App\Classes;

use App\Models\Feed;
use App\Models\UserOauth;

class AuphonicManager
{
    const WEBHOOK_URI = 'webhooks-auphonic';

    const STATUS_DONE = 3;
    const STATUS_INCOMPLETE = 9;

    /**
     * @param $approvals
     * @param  string  $feedId
     * @return \podcasthosting\Auphonic\Client
     * @throws \Exception
     */
    public function getClient($approvals, string $feedId)
    {
        $feed = Feed::owner()
            ->select(['settings.approvals.auphonic'])
            ->whereFeedId($feedId)
            ->first();

        $userOauth = false;

        if (!$approvals) {
            throw new \Exception(trans('auphonic.error_no_approvals_found'));
        }

        $auphonicApprovals = $approvals->filter(function($value) {
            return $value->service === UserOauth::SERVICE_AUPHONIC;
        });

        // Edge case with only a few accounts
        if ($auphonicApprovals->count() > 1) {
            if (isset($feed->settings['approvals']['auphonic'])) {
                foreach($feed->settings['approvals']['auphonic'] as $screenName) {
                    // Choose the selected Auphonic account
                    $userOauth = $auphonicApprovals->firstWhere('screen_name', '=', $screenName);

                    if ($userOauth) {
                        break;
                    }
                }
            }
        }

        if (!$userOauth) {
            $userOauth = $auphonicApprovals->first();
        }

        if (!$userOauth) {
            throw new \Exception(trans('auphonic.error_no_oauth_found'));
        }

        $client = new \podcasthosting\Auphonic\Client();
        $token = $userOauth->token;
        $client->setToken($token);
        $client->setPreset(new \podcasthosting\Auphonic\Client\Preset());

        return $client;
    }
}
