<?php
/**
 * User: fabio
 * Date: 11.05.19
 * Time: 21:28
 */

namespace App\Classes\FeedValidator;

use App\Classes\FeedValidator;
use App\Exceptions\FeedValidatorException;

class Show extends FeedValidator
{
    private $entry;

    /**
     * @return array
     * @throws \Exception
     */
    public function run(): array
    {
        $this->entry = $this->getEntry();

        return parent::run();
    }

    // Entry exists
    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkShowIsSet(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    // IsPublished

    /**
     * @return bool
     * @throws FeedValidatorException
     */
/*    public function checkShowIsPublished(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__);
        }

        return $this->fire(__FUNCTION__, 'success');
    }*/

    // HasEnclosure

    /**
     * @return bool
     * @throws FeedValidatorException
     */
    public function checkShowHasEnclosure(): bool
    {
        if (!$this->entry) {
            return $this->isMissing(__FUNCTION__);
        }

        if (!isset($this->entry['show_media']) || empty($this->entry['show_media'])) {
            return $this->fire(__FUNCTION__, 'error', 'error_validator_show_media_missing', ['title' => $this->entry['title'], 'feed' => $this->feed->feed_id]);
        }

        return $this->fire(__FUNCTION__, 'success');
    }

    /**
     * @param  string  $func
     * @throws FeedValidatorException
     */
    protected function isMissing(string $func)
    {
        return $this->fire($func, 'error', 'error_validator_show_entry_missing', ['feed' => $this->feed->feed_id]);
    }

}
