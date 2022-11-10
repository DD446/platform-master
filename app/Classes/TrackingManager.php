<?php
/**
 * User: fabio
 * Date: 27.08.20
 * Time: 12:36
 */

namespace App\Classes;
use Illuminate\Support\Facades\Http;

class TrackingManager
{
    /**
     * @param  string  $link
     * @param  string  $enclosureLink
     * @return bool
     * @throws \Exception
     */
    public function checkChartable(string $link, string $enclosureLink): bool
    {
        $response = Http::withoutRedirecting()->get($link);

        if (!$response->redirect()) {
            throw new \Exception(trans('feeds.error_tracking_no_redirect'));
        }

        $headers = $response->headers();
        $location = $headers['Location'][0] ?? '';

        if ($location != $enclosureLink) {
            throw new \Exception(trans('feeds.error_tracking_wrong_location'));
        }

        return true;
    }

    /**
     * @param  string  $link
     * @return bool
     * @throws \Exception
     */
    public function checkRms(string $link): bool
    {
        $response = Http::withoutRedirecting()->get($link);

        if ($response->status() > 302) {
            throw new \Exception(trans('feeds.error_tracking_wrong_status'), $response->status());
        }

        return true;
    }

    /**
     * @param  string  $link
     * @return bool
     * @throws \Exception
     */
    public function checkPodtrac(string $link): bool
    {
        $response = Http::withoutRedirecting()->get($link);

        if ($response->status() > 302) {
            throw new \Exception(trans('feeds.error_tracking_wrong_status'), $response->status());
        }

        return true;
    }

    /**
     * @param  string  $link
     * @return bool
     * @throws \Exception
     */
    public function checkAudiotakes(string $link, string $enclosureLink): bool
    {
        $response = Http::withoutRedirecting()->get($link);

        if (!$response->redirect()) {
            throw new \Exception(trans('feeds.error_tracking_no_redirect'));
        }

        $headers = $response->headers();
        $location = $headers['Location'][0] ?? '';

        $response = Http::get($location);
/*
        if ($location != $enclosureLink) {
            throw new \Exception(trans('feeds.error_tracking_wrong_location'));
        }*/

        if (!$response->ok()) {
            throw new \Exception(trans('feeds.error_tracking_wrong_status'));
        }

        return true;
    }
}
