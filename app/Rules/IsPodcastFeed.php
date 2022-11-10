<?php

namespace App\Rules;

use App\Classes\FeedReader;
use Illuminate\Contracts\Validation\Rule;
use Laminas\Feed\Reader\Reader;

class IsPodcastFeed implements Rule
{
    private $noFeed = false;
    private $missingEnclosures = false;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $feed = FeedReader::getCachedFeed($value);
        } catch (\Exception $e) {
            $this->noFeed = $e->getMessage();
            return false;
        }
        $cEnclosures = 0;

        foreach ($feed as $entry) {
            $oEnclosure = $entry->getEnclosure();
            if (isset($oEnclosure->url) && !empty($oEnclosure->url)) {
                $cEnclosures++;
            }
        }

        if ($cEnclosures == 0) {
            $this->missingEnclosures = true;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->missingEnclosures) {
            return trans('feeds.validation_error_missing_enclosures');
        }
        return trans('feeds.validation_error_no_feed', ['message' => $this->noFeed]);
    }
}
