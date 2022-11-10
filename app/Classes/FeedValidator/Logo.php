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
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class Logo extends FeedValidator
{
    private $file;
    private $dimensions;

    /**
     * @return array
     * @throws \Exception
     */
    public function run(): array
    {
        if (isset($this->feed->logo['itunes']) && !empty($this->feed->logo['itunes'])) {
            $this->file = get_file($this->username, $this->feed->logo['itunes']);
            $this->dimensions = [];
            if ($this->file && isset($this->file['path'])) {
                $this->dimensions = getimagesize($this->file['path']);
            }
        }

        return parent::run();
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     * @throws \Exception
     */
    public function checkLogoIsSet(): bool
    {
        if (!isset($this->feed->logo['itunes']) || empty($this->feed->logo['itunes'])) {
            return $this->isMissing(__FUNCTION__);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoExists(): bool
    {
        $file = $this->file;

        if (!$file) {
            return $this->isMissing(__FUNCTION__);
        }

        if (!File::exists($file['path'])) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_missing');
        }

        return $this->fire(__FUNCTION__, 'success');
    }


    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoExtension(): bool
    {
        // LogoExtension
        $file = $this->file;

        if (!$file) {
            return $this->isMissing(__FUNCTION__);
        }

        $ext = File::extension($file['name']);

        if (!$ext) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_missing_extension');
        }

        if (!in_array($ext, ['png', 'jpg'])) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_wrong_extension', ['feed' => $this->feed->feed_id, 'extension' => $ext]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoDimensionSquare(): bool
    {
        $dimensions = $this->dimensions;

        if (!$dimensions) {
            return $this->isMissing(__FUNCTION__);
        }

        $width = $dimensions[0];
        $height = $dimensions[1];

        if ($height != $width) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_not_square', ['feed' => $this->feed->feed_id, 'width' => $width, 'height' => $height]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoDimensionMinimumSize(): bool
    {
        $dimensions = $this->dimensions;

        if (!$dimensions) {
            return $this->isMissing(__FUNCTION__);
        }

        $height = $dimensions[1];

        if ($height < 1400) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_too_small', ['feed' => $this->feed->feed_id, 'height' => $height]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoDimensionMaximumSize(): bool
    {
        $dimensions = $this->dimensions;

        if (!$dimensions) {
            return $this->isMissing(__FUNCTION__);
        }

        $height = $dimensions[1];

        if ($height > 3000) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_too_big', ['feed' => $this->feed->feed_id, 'height' => $height]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoFileSize(): bool
    {
        $file = $this->file;

        if (!$file) {
            return $this->isMissing(__FUNCTION__);
        }

        // LogoFileSize
        if ($file['byte'] > 1572864) {
            return $this->fire(__FUNCTION__, 'warning', 'error_channel_image_filesize_too_big', ['feed' => $this->feed->feed_id, 'filesizeFormatted' => $file['size']]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoType(): bool
    {
        $dimensions = $this->dimensions;

        if (!$dimensions) {
            return $this->isMissing(__FUNCTION__);
        }

        // LogoType
        if (!in_array($dimensions['mime'], ['image/png', 'image/jpeg'])
            || !in_array($dimensions[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_wrong_type', ['feed' => $this->feed->feed_id, 'type' => $dimensions['mime']]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoColorspace(): bool
    {
        $dimensions = $this->dimensions;

        if (!$dimensions) {
            return $this->isMissing(__FUNCTION__);
        }

        // `channels` will be 3 for RGB pictures and 4 for CMYK pictures
        if (isset($dimensions['channels']) && $dimensions['channels'] != 3) {
            return $this->fire(__FUNCTION__, 'error', 'error_channel_image_wrong_colorspace');
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoBackgroundColor(): bool
    {
        $file = $this->file;

        if (!$file) {
            return $this->isMissing(__FUNCTION__);
        }

        $dimensions = $this->dimensions;

        // Make sure the picture does not have a transparent background
        if ($dimensions['mime'] == 'image/png') {
            if (is_alpha_png($file['path'])) {
                if (has_transparency($file['path'])) {
                    return $this->fire(__FUNCTION__, 'error', 'error_channel_image_transparent_background');
                }
            }
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkLogoAvailability(): bool
    {
        $file = $this->file;

        if (!$file) {
            return $this->isMissing(__FUNCTION__);
        }

        // LogoIsReachable
        $url = get_image_uri($this->feed->feed_id, $file['name'], $this->feed->domain);
        $res = Http::head($url);

        switch ($res->status()) {
            case 200:
            case 201:
                // Everything is fine here
                break;
            case 301:
            case 302:
                    return $this->fire(__FUNCTION__, 'error', 'error_channel_image_redirect_detected', ['goal' => $res->header('Location')[0]]);
                break;
            case 404:
                    // Try to write the feed and re-create symlinks (most likely reason image is not reachable)
                    $this->writeFeed();

                    $res = Http::head($url);

                    if ($res->status() == 404) {
                        return $this->fire(__FUNCTION__, 'error', 'error_channel_image_not_found', ['url' => $url]);
                    } elseif ($res->status() >= 300) {
                        return $this->fire(__FUNCTION__, 'error', 'error_channel_image_unexpected_status_code', ['code' => $res->status()]);
                    }
                break;
            default:
                return $this->fire(__FUNCTION__, 'error', 'error_channel_image_unexpected_status_code', ['code' => $res->status()]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @throws FeedValidatorException
     */
    private function isMissing($func)
    {
        return $this->fire($func, 'error', 'error_channel_image_not_set', ['feed' => $this->feed->feed_id]);
    }
}
