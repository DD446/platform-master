<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;
use Khill\Duration\Duration;

class FeedJsonResource extends JsonResource
{
    /**
     * @return string
     */
    protected function getLogoLink(): string
    {
        $logo = '';

        try {
            if (isset($this->logo['itunes']) && is_numeric($this->logo['itunes'])) {
                $file = get_file($this->username, $this->logo['itunes']);
                $logo =  get_image_uri($this->feed_id, $file['name'], $this->domain);
            }
        } catch (\Exception $e) {
        }

        return $logo;
    }

    /**
     * @param  bool  $original
     * @return string
     */
    protected function getFeedLink(bool $original = false): string
    {
        if (isset($this->settings['protection']) && $this->settings['protection'] == '1' && !$original) {
            $uri = get_feed_uri($this->settings['protection_id'], $this->domain);
        } else if (isset($this->domain['feed_redirect']) && $this->domain['feed_redirect'] && !$original) {
            $uri = $this->domain['feed_redirect'];
        } else {
            $uri = get_feed_uri($this->feed_id, $this->domain);
        }

        return $uri;
    }

    /**
     * @param  bool  $original
     * @return string
     */
    protected function getWebsiteLink(bool $original = false): string
    {
        if(!isset($this->domain['website_type']) || $this->domain['website_type'] === false) {
            $uri = '';
        } else if (isset($this->domain['website_redirect']) && $this->domain['website_redirect'] && !$original) {
            $uri = $this->domain['website_redirect'];
        } else {
            $uri = $this->domain['hostname'];
        }

        return $uri;
    }

    /**
     * @return string
     */
    protected function getLogo(): string
    {
        $logo = '';

        try {
            if (isset($this->logo['itunes']) && is_numeric($this->logo['itunes'])) {
                $file = get_file($this->username, $this->logo['itunes']);
                if ($file && isset($file['name'])) {
                    $logo = $file['name'];
                }
            }
        } catch (\Exception $e) {
        }

        return $logo;
    }

    /**
     * @return string
     */
    protected function getShowImage(): string
    {
        $logo = '';

        try {
            if (isset($this->itunes['logo']) && is_numeric($this->itunes['logo'])) {
                $file = get_file($this->username, $this->itunes['logo']);
                if ($file && isset($file['name'])) {
                    $logo = $file['name'];
                }
            }
        } catch (\Exception $e) {
        }

        return $logo;
    }

    /**
     * @param  string  $feed
     * @param $domain
     * @return string
     */
    protected function getShowImageLink(string $feed, $domain): string
    {
        $logo = $this->getShowImage();

        if (!$logo) {
            return $logo;
        }

        return get_image_uri($feed, $logo, $domain);
    }

    /**
     * @return string
     */
    protected function getShowMedia(): string
    {
        $media = '';

        try {
            if (isset($this->show_media) && is_numeric($this->show_media)) {
                $file = get_file($this->username, $this->show_media);

                if ($file && isset($file['name'])) {
                    $media = $file['name'];
                }
            }
        } catch (\Exception $e) {}

        return $media;
    }

    /**
     * @param  string  $feed
     * @param $domain
     * @return string
     */
    protected function getShowMediaLink(string $feed, $domain): string
    {
        $media = $this->getShowMedia();

        if (!$media) {
            return $media;
        }

        return get_enclosure_uri($feed, $media, $domain);
    }

    /**
     * @param  string  $mediaLink
     * @return string
     */
    protected function getType(string $mediaLink): string
    {
        $ext = File::extension(parse_url($mediaLink, PHP_URL_PATH));

        switch ($ext) {
            case 'mp3':
            case 'm4a':
            case 'aac':
            case 'ogg':
            case 'oga':
            case 'wav':
            case 'weba':
            case 'flac':
                return 'audio';
            case 'mp4':
            case 'ogv':
            case 'webm':
            case 'mov':
            case 'avi':
                return 'video';
            case 'png':
            case 'jpeg':
            case 'jpg':
            case 'gif':
                return 'image';
            default:
                return 'unknown';
        }
    }

    /**
     * @param $duration
     * @return string
     */
    protected function getDurationFormatted($duration)
    {
        $duration = new Duration($duration);

        return $duration->humanize();
    }
}
