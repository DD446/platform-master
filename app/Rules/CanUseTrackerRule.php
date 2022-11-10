<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;

class CanUseTrackerRule implements Rule
{
    /**
     * @var Package
     */
    private Package $newPackage;

    /**
     * @var string
     */
    private $message;

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
        $newPackageHasFeatureAds = has_package_feature($this->newPackage, Package::FEATURE_ADS);

        // Only need to test if user has used this feature if new package the feature is NOT available in new package
        if (!$newPackageHasFeatureAds) {
            // Get all feeds from user
            $feeds = Feed::owner()->get(['settings']);
            // Extract relevant settings
            $usesTrackingFeature = $feeds->map(function ($f) {
                return isset($f->settings['chartable']) && (bool) $f->settings['chartable']
                    || isset($f->settings['rms']) && (bool) $f->settings['rms']
                    || isset($f->settings['podcorn']) && (bool) $f->settings['podcorn'];
            })->reject(function($v) { return $v !== true; })->count();

            if ($usesTrackingFeature > 0) {
                // TODO: Explain more verbosely which tracking user is using
                $this->message = trans('package.message_error_cannot_use_tracking_feature');
                return false;
            }
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
        return $this->message;
    }
}
