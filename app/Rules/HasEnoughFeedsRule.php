<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Package;

class HasEnoughFeedsRule implements Rule
{
    /**
     * @var Package
     */
    private $newPackage;

    private $feeds;

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
        // Returns total (available) feeds incl. extras for new package
        // and used feeds right now
        $aFeeds = get_package_feature_feeds($this->newPackage, auth()->user());
        $this->feeds = $aFeeds;

        return $aFeeds['used'] <= $aFeeds['total'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('package.message_error_not_enough_feeds', ['used' => $this->feeds['used'], 'over' => $this->feeds['used'] - $this->feeds['total'], 'total' => $this->feeds['total']]);
    }
}
