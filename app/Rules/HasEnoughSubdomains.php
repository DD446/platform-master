<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;

class HasEnoughSubdomains implements Rule
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
        $allowedSubdomainsInNewPackage = get_package_units($this->newPackage, Package::FEATURE_SUBDOMAINS);
        $username = auth()->user()->username;

        $countSubdomainsInUse = Feed::distinct('domain.hostname')
            ->where('username', '=', $username)
            ->where('domain.website_type', '!=', false)
            ->where('domain.is_custom', '=', false)
            ->where(function ($query) {
                return $query->where('domain.website_redirect', '=', false)
                    ->orWhere(function ($query) {
                        return $query->whereNull('domain.website_redirect');
                    });
            })
            ->where('domain.subdomain', 'not like', "$username.%")
            ->count();

        if ($countSubdomainsInUse === 0) {
            // Nothing further to test
            return true;
        }

        // This is weired and shouldn´t happen but ok
        if (!$allowedSubdomainsInNewPackage && $countSubdomainsInUse > 0) {
            $this->message = trans('package.message_error_subdomains_not_available',
                ['used' => $countSubdomainsInUse]);
            return false;
        }

        if ($countSubdomainsInUse > $allowedSubdomainsInNewPackage) {
            $this->message = trans('package.message_error_not_enough_subdomains_available',
                ['used' => $countSubdomainsInUse, 'available' => $allowedSubdomainsInNewPackage, 'remove' => $countSubdomainsInUse - $allowedSubdomainsInNewPackage]);
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
