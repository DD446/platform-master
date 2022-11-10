<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;

class HasProtectedFeedsRule implements Rule
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

        // User does not use the protection feature
        // so this test always passes
        if (!$feeds->contains(function ($feed, $key) {
            return isset($feed->settings['protection']) && $feed->settings['protection'] == 1;
        })) {
            return true;
        }

        // Otherwise the new package has to offer the protection feature
        return has_package_feature($this->newPackage, Package::FEATURE_PROTECTION);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('package.message_error_no_protection_feature');
    }
}
