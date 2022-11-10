<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Package;

class HasEnoughMembersRule implements Rule
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
        $allowedMembersInNewPackage = get_package_units($this->newPackage, Package::FEATURE_MEMBERS);
        $aMemberCountNew = get_package_feature_members($this->newPackage, auth()->user());
        $aMemberCount = get_package_feature_members(auth()->user()->package, auth()->user());

        if (!$allowedMembersInNewPackage && $aMemberCount['used'] > 0) {
            $packageName = trans_choice('package.package_name', $this->newPackage->package_name);
            $this->message = trans('package.message_error_members_not_available', ['used' => $aMemberCount['used'], 'packageName' => $packageName]);
            return false;
        }

        if ($aMemberCount['used'] > $aMemberCountNew['total']) {
            $packageName = trans_choice('package.package_name', $this->newPackage->package_name);
            $this->message = trans('package.message_error_not_enough_members_available', ['used' => $aMemberCount['used'], 'available' => $aMemberCountNew['total'], 'remove' => $aMemberCount['used'] - $aMemberCountNew['total'], 'packageName' => $packageName]);
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
        return $this->message;
    }
}
