<?php
/**
 * User: fabio
 * Date: 11.05.19
 * Time: 21:28
 */

namespace App\Classes\FeedValidator;


use Buzz\Browser;
use Buzz\Client\Curl;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Classes\FeedValidator;
use App\Exceptions\FeedValidatorException;
use App\Exceptions\FeedValidatorWarning;
use App\Models\Feed;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class Channel extends FeedValidator
{
    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws \Exception
     */
    public function checkExportedFileExists(): bool
    {
        $file = get_user_feed_filename($this->username, $this->feed->feed_id);

        if (!File::exists($file)) {
            // Try to write the feed to file if it does not exist,yet
            $this->writeFeed();

            // If it still is missing throw an exception
            if (!File::exists($file)) {
                return $this->fire(__FUNCTION__, 'error', 'error_validator_file_missing');

            }
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedIsReachable(): bool
    {
        $url = get_feed_uri($this->feed->feed_id, $this->feed->domain);
        $res = Http::head($url);

        switch ($res->status()) {
            case 200:
            case 201:
                // Everything is fine here
                break;
            case 301:
                return $this->fire(__FUNCTION__, 'error', 'warning_validator_feed_redirected', ['link' => $this->feed->domain['feed_redirect']]);
                break;
            case 401:
                return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_protected');
                break;
            default:
                return $this->fire(__FUNCTION__, 'error', 'error_validator_unexpected_status_code', ['code' => $res->status(), 'reason' => $res->body()]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedIsNotRedirected(): bool
    {
        if (!empty($this->feed->domain->feed_redirect)) {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_redirected', ['link' => $this->feed->domain['feed_redirect']]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedIsNotBlocked(): bool
    {
        // Check crawler restrictions
        if ($this->feed->itunes['block'] != 'no') {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_blocked', ['feed' => $this->feed->feed_id]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedIsNotComplete(): bool
    {
        // Check itunes:complete (Beendet/vollständig)
        if (isset($this->feed->itunes['complete']) && $this->feed->itunes['complete'] === 'yes') {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_complete', ['feed' => $this->feed->feed_id]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedHasNoNewFeedUrl(): bool
    {

        if (isset($this->feed->itunes['new-feed-url']) && !empty($this->feed->itunes['new-feed-url'])) {

            $feedUrl = get_feed_uri($this->feed->feed_id, $this->feed->domain);

            if ($feedUrl === $this->feed->itunes['new-feed-url']) {
                return $this->fire(__FUNCTION__, 'error', 'error_validator_feed_newfeedurl');

            }

            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_newfeedurl', ['url' => $this->feed->itunes['new-feed-url'], 'feed' => $this->feed->feed_id]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    public function checkFeedEpisodeCountSetting(): bool
    {
        $fe = $this->feed->settings['feed_entries'] ?? Feed::FEED_COUNT_DEFAULT;
        // Check channel[settings][feed_entries]
        if ($fe != Feed::FEED_COUNT_UNLIMITED && count($this->feed->entries) > $fe) {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_feed_episode_count_setting', ['feed' => $this->feed->feed_id]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    // TODO Check Feed Chartable.com integration
/*    public function checkChartableIntegrationWorks(): bool
    {
        return $this->fire(__FUNCTION__, 'success');
    }*/
}
