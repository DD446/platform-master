<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;
use App\Models\Show;

class HasScheduledPostsRule implements Rule
{
    /**
     * @var Package
     */
    private $newPackage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Package $newPackage)
    {
        $this->newPackage = $newPackage;
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
        $feeds = Feed::owner()->get();

        $hasScheduledShow = false;

        foreach ($feeds as $feed) {
            if ($feed->shows->contains(function ($show, $key) {
                return $show['is_public'] == Show::PUBLISH_FUTURE;
            })) {
                $hasScheduledShow = true;
                break;
            }
        }

        if (!$hasScheduledShow) {
            return true;
        }

        // Otherwise the new package has to offer the scheduled posts feature
        return has_package_feature($this->newPackage, Package::FEATURE_SCHEDULER);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('package.message_error_no_scheduler_feature');
    }
}
