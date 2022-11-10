<?php

namespace App\Rules;

use App\Models\PackageFeatureMapping;
use App\Models\Space;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Package;

class HasEnoughSpaceRule implements Rule
{
    /**
     * @var Package
     */
    private $newPackage;

    /**
     * @var array
     */
    private $space;

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
        // Returns total (available) space for new package
        // and used space right now
        $user = auth()->user();
        $aSpace = get_user_space($user);

        $this->space = $aSpace;

        $total = Space::available()->whereUserId($user->id)->sum('space');
        $available = PackageFeatureMapping::where('package_feature_id', '=', 3)->where('package_id', '=', $this->newPackage->package_id)->value('units');
        $used = $total - $available;
        $this->space['over'] = $used-$total;

        return $used <= $total;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $over = get_size_readable($this->space['over']);
        return trans('package.message_error_not_enough_space', ['used' => $this->space['used'], 'over' => $over, 'total' => $this->space['total']]);
    }
}
