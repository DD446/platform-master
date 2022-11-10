<?php

namespace App\Rules;

use App\Models\Feed;
use App\Models\User;
use App\Models\UserForbidden;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class UniqueSubdomain implements Rule
{
    private $name;

    private string $username;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $username)
    {
        $this->username = $username;
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
        $this->name = $value;

        if (Str::lower($value) === Str::lower($this->username)) {
            return true;
        }

        if (User::whereUsername($value)->get()->count() > 0) {
            return false;
        }

        if (UserForbidden::whereUsername($value)->count() > 0) {
            return false;
        }

        return Feed::where(function($query) use ($value) {
                    $query->where('domain.subdomain', '=', $value)
                        ->orWhere('domain.subdomain', '=', Str::lower($value));
                })
                ->where('username', '!=', $this->username)
                ->get()
                ->count() < 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('feeds.error_message_subdomain_not_unique', ['name' => $this->name]);
    }
}
