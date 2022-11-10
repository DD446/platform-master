<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Feed;
use App\Models\Package;

class HasEnoughBlogs implements Rule
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
        $allowedBlogsInNewPackage = get_package_units($this->newPackage, Package::FEATURE_BLOGS);
        $username = auth()->user()->username;
        $countBlogsInUse = Feed::distinct('domain.hostname')
            ->where('username', '=', $username)
            ->where('domain.website_type', '!=', false)
            ->where(function ($query) {
                return $query->where('domain.website_redirect', '=', false)
                    ->orWhere(function ($query) {
                        return $query->whereNull('domain.website_redirect');
                    });
            })
            ->count();

        if ($countBlogsInUse === 0) {
            return true;
        }

        if (!$allowedBlogsInNewPackage && $countBlogsInUse > 0) {
            $this->message = trans('package.message_error_blogs_not_available',
                ['used' => $countBlogsInUse]);
            return false;
        }

        if ($countBlogsInUse > $allowedBlogsInNewPackage) {
            $this->message = trans('package.message_error_not_enough_blogs_available',
                ['used' => $countBlogsInUse, 'available' => $allowedBlogsInNewPackage, 'remove' => $countBlogsInUse - $allowedBlogsInNewPackage]);
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
