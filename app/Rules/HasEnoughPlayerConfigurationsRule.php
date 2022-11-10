<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Package;

class HasEnoughPlayerConfigurationsRule implements Rule
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
        $allowedPlayerConfigurationsInNewPackage = get_package_units($this->newPackage, Package::FEATURE_PLAYER_CONFIGURATION);
        $aPlayerConfigurationCountNew = get_package_feature_player_configuration($this->newPackage, auth()->user());
        $aPlayerConfigurationCount = get_package_feature_player_configuration(auth()->user()->package, auth()->user());

        if (!$allowedPlayerConfigurationsInNewPackage && $aPlayerConfigurationCount['used'] > 0) {
            $packageName = trans_choice('package.package_name', $this->newPackage->package_name);
            $this->message = trans('package.message_error_player_configurations_not_available', ['used' => $aPlayerConfigurationCount['used'], 'packageName' => $packageName]);
            return false;
        }

        if ($aPlayerConfigurationCount['used'] > $aPlayerConfigurationCountNew['total']) {
            $packageName = trans_choice('package.package_name', $this->newPackage->package_name);
            $this->message = trans('package.message_error_not_enough_player_configurations_available', ['used' => $aPlayerConfigurationCount['used'], 'available' => $aPlayerConfigurationCountNew['total'], 'remove' => $aPlayerConfigurationCount['used'] - $aPlayerConfigurationCountNew['total'], 'packageName' => $packageName]);
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
