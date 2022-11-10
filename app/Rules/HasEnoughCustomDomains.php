<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;

class HasEnoughCustomDomains implements Rule
{
    /**
     * @var Package
     */
    private $newPackage;

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
        $allowedDomainsInNewPackage = get_package_units($this->newPackage, Package::FEATURE_DOMAINS);

        $countCustomDomainsInUse = Feed::distinct('domain.hostname')
            ->where('username', '=', auth()->user()->username)
            ->whereIn('domain.is_custom', [1, '1', true])
            ->count();

        if ($countCustomDomainsInUse > 0 && !$allowedDomainsInNewPackage) {
            $this->message = trans('package.message_error_no_custom_domains_available', ['used' => $countCustomDomainsInUse]);
            return false;
        }

        if ($countCustomDomainsInUse > $allowedDomainsInNewPackage) {
            $this->message = trans('package.message_error_not_enough_custom_domains_available',
                ['used' => $countCustomDomainsInUse, 'available' => $allowedDomainsInNewPackage, 'remove' => $countCustomDomainsInUse - $allowedDomainsInNewPackage]);
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
