<?php
/**
 * User: fabio
 * Date: 11.05.19
 * Time: 21:28
 */

namespace App\Classes\FeedValidator;

use Illuminate\Support\Facades\File;
use App\Classes\FeedValidator;
use App\Exceptions\FeedValidatorException;

class Enclosure extends FeedValidator
{
    private $entry;

    /**
     * @return array
     * @throws \Exception
     */
    public function run(): array
    {
        $this->entry = $this->getEntry();

        if (isset($this->entry['show_media']) && !empty($this->entry['show_media'])) {
            $this->file = get_file($this->username, $this->entry['show_media']);
        }

        return parent::run();
    }

    // EnclosureFileExists
    public function checkEnclosureFileExists(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__, 'entry');
        }

        if (!$this->file) {
            return $this->isMissing(__FUNCTION__);
        }

        if (!$this->file || !File::exists($this->file['path'])) {
            return $this->fire(__FUNCTION__, 'error', 'error_validator_enclosure_file_missing');
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    // EnclosureExtension
    public function checkEnclosureHasExtension(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__, 'entry');
        }

        if (!$this->file) {
            return $this->isMissing(__FUNCTION__);
        }

        // Check file has an extension
        $extension = File::extension($this->file['path']);

        if (!$extension) {
            return $this->fire(__FUNCTION__, 'error', 'error_validator_enclosure_file_extension_missing');
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    // Check file has an extension
    public function checkEnclosureType(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__, 'entry');
        }

        if (!$this->file) {
            return $this->isMissing(__FUNCTION__);
        }

        // Check extension is not .wav - issue warning
        $extension = File::extension($this->file['path']);

        if ($extension == 'wav') {
            return $this->fire(__FUNCTION__, 'warning', 'error_validator_enclosure_file_extension_wav', ['file' => $this->file['name']]);
        }

        if (in_array($extension, ['MP3', 'M4A', 'AAC', 'OGG', 'MP4'])) {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_enclosure_file_extension', ['file' => $this->file['name']]);
        }

        if (!in_array($extension, ['mp3', 'm4a', 'aac', 'ogg', 'mp4'])) {
            return $this->fire(__FUNCTION__, 'warning', 'warning_validator_enclosure_file_extension', ['file' => $this->file['name']]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    // EnclosureIsReachable
    public function checkEnclosureAvailability(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__, 'entry');
        }

        if (!$this->file) {
            return $this->isMissing(__FUNCTION__);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @param  string  $func
     * @throws FeedValidatorException
     */
    protected function isMissing(string $func, string $type = 'file')
    {
        if ($type == 'entry') {
            return $this->fire($func, 'error', 'error_validator_show_entry_missing', ['feed' => $this->feed->feed_id]);
        } else {
            return $this->fire($func, 'error', 'error_validator_show_media_missing', ['title' => $this->entry['title'], 'feed' => $this->feed->feed_id]);
        }
    }

}
