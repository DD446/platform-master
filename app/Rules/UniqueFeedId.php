<?php

namespace App\Rules;

use App\Models\Feed;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class UniqueFeedId implements Rule
{
    private $name;

    private string $username;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($username)
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

        if (in_array($this->name, ['feed', 'podcast'])) {
            return false;
        }

        return Feed::where(function($query) {
                    $query->whereFeedId($this->name)
                        ->orWhere('feed_id', '=', Str::lower($this->name));
                })
                ->count() < 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('feeds.validation_error_feed_id_duplicate', ['name' => $this->name]);
    }
}
